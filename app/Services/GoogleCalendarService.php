<?php

namespace App\Services;

use App\Models\GoogleAccount;
use App\Models\GoogleCalendarEvent;
use App\Models\JadwalKegiatan;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected Client $httpClient;
    protected GoogleTokenService $tokenService;

    public function __construct(GoogleTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
        $this->httpClient = new Client(['verify' => false]);
    }

    public function syncEvents(User $user, ?string $calendarId = 'primary'): array
    {
        $account = $user->googleAccount;
        if (! $account || ! $account->isCalendarConnected()) {
            return ['events' => 0, 'error' => 'Calendar not connected'];
        }

        $token = $this->tokenService->getAccessToken($account);
        if (! $token) {
            return ['events' => 0, 'error' => 'Token refresh failed'];
        }

        try {
            $timeMin = now()->subDays(30)->startOfDay()->toIso8601String();
            $timeMax = now()->addDays(90)->endOfDay()->toIso8601String();

            $events = $this->fetchEvents($token, $calendarId, $timeMin, $timeMax);
            $synced = 0;

            foreach ($events as $eventData) {
                if (isset($eventData['status']) && $eventData['status'] === 'cancelled') {
                    $this->handleCancelledEvent($user, $eventData);
                    continue;
                }

                $start = $eventData['start'] ?? [];
                $end = $eventData['end'] ?? [];

                $isAllDay = isset($start['date']);

                GoogleCalendarEvent::updateOrCreate(
                    ['user_id' => $user->id, 'external_id' => $eventData['id']],
                    [
                        'google_account_id' => $account->id,
                        'calendar_id' => $calendarId,
                        'title' => $eventData['summary'] ?? 'Tanpa judul',
                        'description' => $eventData['description'] ?? null,
                        'location' => $eventData['location'] ?? null,
                        'start_datetime' => $isAllDay ? null : ($start['dateTime'] ?? null),
                        'end_datetime' => $isAllDay ? null : ($end['dateTime'] ?? null),
                        'is_all_day' => $isAllDay,
                        'start_date' => $start['date'] ?? null,
                        'end_date' => $end['date'] ?? null,
                        'recurring_event_id' => $eventData['recurringEventId'] ?? null,
                        'html_link' => $eventData['htmlLink'] ?? null,
                        'source_label' => 'calendar',
                        'synced_at' => now(),
                    ]
                );
                $synced++;
            }

            $account->update(['calendar_connected_at' => $account->calendar_connected_at ?? now()]);

            return ['events' => $synced];
        } catch (\Throwable $e) {
            Log::error('Calendar sync failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return ['events' => 0, 'error' => $e->getMessage()];
        }
    }

    public function exportTask(User $user, JadwalKegiatan $jadwal): ?string
    {
        $account = $user->googleAccount;
        if (! $account || ! $account->isCalendarConnected()) {
            return null;
        }

        if ($jadwal->synced_to_calendar && $jadwal->calendar_event_id) {
            return $this->updateCalendarEvent($user, $jadwal);
        }

        $token = $this->tokenService->getAccessToken($account);
        if (! $token) {
            return null;
        }

        try {
            $eventBody = $this->buildEventFromJadwal($jadwal);

            $response = $this->httpClient->post(
                'https://www.googleapis.com/calendar/v3/calendars/primary/events',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $eventBody,
                ]
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $jadwal->update([
                'synced_to_calendar' => true,
                'calendar_event_id' => $data['id'],
            ]);

            return $data['id'] ?? null;
        } catch (\Throwable $e) {
            Log::warning('Task export to Calendar failed', [
                'user_id' => $user->id,
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function exportCollegeSchedule(User $user, \App\Models\CollegeSchedule $schedule): ?string
    {
        $account = $user->googleAccount;
        if (! $account || ! $account->isCalendarConnected()) {
            return null;
        }

        $token = $this->tokenService->getAccessToken($account);
        if (! $token) {
            return null;
        }

        try {
            $rrule = $this->buildRecurrenceRule($schedule);

            $eventBody = [
                'summary' => $schedule->mata_kuliah,
                'description' => trim(($schedule->dosen ? "Dosen: {$schedule->dosen}\n" : '') . ($schedule->catatan ?? '')),
                'location' => $schedule->lokasi,
                'start' => [
                    'dateTime' => now()->modify('next ' . $schedule->hari)->format('Y-m-d') . 'T' . $schedule->jam_mulai . ':00',
                    'timeZone' => 'Asia/Jakarta',
                ],
                'end' => [
                    'dateTime' => now()->modify('next ' . $schedule->hari)->format('Y-m-d') . 'T' . $schedule->jam_selesai . ':00',
                    'timeZone' => 'Asia/Jakarta',
                ],
                'recurrence' => [$rrule],
                'colorId' => $this->colorToCalendarId($schedule->warna),
            ];

            $response = $this->httpClient->post(
                'https://www.googleapis.com/calendar/v3/calendars/primary/events',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $eventBody,
                ]
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $schedule->update([
                'synced_to_calendar' => true,
                'calendar_event_id' => $data['id'],
            ]);

            return $data['id'] ?? null;
        } catch (\Throwable $e) {
            Log::warning('College schedule export failed', [
                'user_id' => $user->id,
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function updateCalendarEvent(User $user, JadwalKegiatan $jadwal): ?string
    {
        $account = $user->googleAccount;
        $token = $this->tokenService->getAccessToken($account);
        if (! $token) {
            return null;
        }

        try {
            $eventBody = $this->buildEventFromJadwal($jadwal);

            $response = $this->httpClient->put(
                "https://www.googleapis.com/calendar/v3/calendars/primary/events/{$jadwal->calendar_event_id}",
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $eventBody,
                ]
            );

            $data = json_decode($response->getBody()->getContents(), true);

            return $data['id'] ?? $jadwal->calendar_event_id;
        } catch (\Throwable $e) {
            Log::warning('Calendar event update failed', [
                'user_id' => $user->id,
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function buildEventFromJadwal(JadwalKegiatan $jadwal): array
    {
        $event = [
            'summary' => $jadwal->judul,
            'description' => trim(($jadwal->course_name ? "Mata Kuliah: {$jadwal->course_name}\n" : '') . ($jadwal->deskripsi ?? '')),
            'location' => $jadwal->lokasi_atau_link,
        ];

        if ($jadwal->is_all_day) {
            $event['start'] = ['date' => $jadwal->waktu_pelaksanaan->toDateString()];
            $event['end'] = ['date' => $jadwal->waktu_pelaksanaan->addDay()->toDateString()];
        } else {
            $event['start'] = [
                'dateTime' => $jadwal->waktu_pelaksanaan->toIso8601String(),
                'timeZone' => 'Asia/Jakarta',
            ];
            $event['end'] = [
                'dateTime' => ($jadwal->deadline_at ?? $jadwal->waktu_pelaksanaan->copy()->addHour())->toIso8601String(),
                'timeZone' => 'Asia/Jakarta',
            ];
        }

        return $event;
    }

    protected function buildRecurrenceRule(\App\Models\CollegeSchedule $schedule): string
    {
        $days = match (strtolower($schedule->hari)) {
            'senin' => 'MO',
            'selasa' => 'TU',
            'rabu' => 'WE',
            'kamis' => 'TH',
            'jumat' => 'FR',
            'sabtu' => 'SA',
            'minggu' => 'SU',
            default => 'MO',
        };

        $rule = "RRULE:FREQ=WEEKLY;BYDAY={$days}";

        if ($schedule->semester_akhir) {
            $rule .= ';UNTIL=' . $schedule->semester_akhir->format('Ymd') . 'T235959Z';
        }

        return $rule;
    }

    protected function colorToCalendarId(?string $hex): string
    {
        $colors = [
            '#3B82F6' => '9',
            '#EF4444' => '11',
            '#10B981' => '10',
            '#F59E0B' => '5',
            '#8B5CF6' => '3',
            '#EC4899' => '4',
            '#06B6D4' => '7',
        ];

        return $colors[$hex] ?? '1';
    }

    protected function handleCancelledEvent(User $user, array $eventData): void
    {
        GoogleCalendarEvent::where('user_id', $user->id)
            ->where('external_id', $eventData['id'])
            ->delete();
    }

    protected function fetchEvents(string $token, string $calendarId, string $timeMin, string $timeMax): array
    {
        $events = [];
        $pageToken = null;

        do {
            $params = [
                'timeMin' => $timeMin,
                'timeMax' => $timeMax,
                'singleEvents' => 'true',
                'maxResults' => 250,
            ];
            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }

            $response = $this->httpClient->get(
                "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events",
                [
                    'headers' => ['Authorization' => 'Bearer ' . $token],
                    'query' => $params,
                ]
            );

            $data = json_decode($response->getBody()->getContents(), true);
            $events = array_merge($events, $data['items'] ?? []);
            $pageToken = $data['nextPageToken'] ?? null;
        } while ($pageToken);

        return $events;
    }

    public function disconnect(User $user): void
    {
        $account = $user->googleAccount;
        if (! $account) {
            return;
        }

        $account->update([
            'calendar_connected_at' => null,
        ]);

        GoogleCalendarEvent::where('user_id', $user->id)->delete();
    }
}
