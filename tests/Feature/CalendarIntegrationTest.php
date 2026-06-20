<?php

namespace Tests\Feature;

use App\Models\CollegeSchedule;
use App\Models\GoogleAccount;
use App\Models\GoogleCalendarEvent;
use App\Models\User;
use App\Services\GoogleCalendarService;
use App\Services\GoogleTokenService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CalendarIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_calendar_event_for_date_scope_does_not_leak_cross_user_all_day_events(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $accountA = GoogleAccount::create([
            'user_id' => $userA->id,
            'google_account_email' => 'userA@gmail.com',
            'access_token_encrypted' => 'encrypted',
            'refresh_token_encrypted' => 'encrypted',
            'token_expires_at' => now()->addHour(),
            'scopes' => [],
        ]);

        $accountB = GoogleAccount::create([
            'user_id' => $userB->id,
            'google_account_email' => 'userB@gmail.com',
            'access_token_encrypted' => 'encrypted',
            'refresh_token_encrypted' => 'encrypted',
            'token_expires_at' => now()->addHour(),
            'scopes' => [],
        ]);

        // User B has an all-day event today
        GoogleCalendarEvent::create([
            'user_id' => $userB->id,
            'google_account_id' => $accountB->id,
            'external_id' => 'event-b-1',
            'calendar_id' => 'primary',
            'title' => 'User B All Day Event',
            'is_all_day' => true,
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'synced_at' => now(),
        ]);

        // User A has a regular event tomorrow
        GoogleCalendarEvent::create([
            'user_id' => $userA->id,
            'google_account_id' => $accountA->id,
            'external_id' => 'event-a-1',
            'calendar_id' => 'primary',
            'title' => 'User A Event',
            'is_all_day' => false,
            'start_datetime' => now()->addDay(),
            'end_datetime' => now()->addDay()->addHour(),
            'synced_at' => now(),
        ]);

        // Querying events for User A today should return 0 events, and MUST NOT leak User B's all-day event
        $eventsForA = GoogleCalendarEvent::ownedBy($userA)->forDate(now())->get();

        $this->assertCount(0, $eventsForA);
    }

    public function test_college_schedule_jam_mulai_and_jam_selesai_accessors_return_formatted_time(): void
    {
        $user = User::factory()->create();

        $schedule = CollegeSchedule::create([
            'user_id' => $user->id,
            'mata_kuliah' => 'Pemrograman Web',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:30:00',
            'is_active' => true,
        ]);

        // Assert that the fields are formatted to 'H:i' strings via accessors
        $this->assertSame('08:00', $schedule->jam_mulai);
        $this->assertSame('10:30', $schedule->jam_selesai);
    }

    public function test_google_calendar_service_export_college_schedule_maps_indonesian_days(): void
    {
        $user = User::factory()->create();
        $account = GoogleAccount::create([
            'user_id' => $user->id,
            'google_account_email' => 'user@gmail.com',
            'access_token_encrypted' => 'encrypted',
            'refresh_token_encrypted' => 'encrypted',
            'token_expires_at' => now()->addHour(),
            'scopes' => ['https://www.googleapis.com/auth/calendar'],
            'calendar_connected_at' => now(),
        ]);

        $schedule = CollegeSchedule::create([
            'user_id' => $user->id,
            'mata_kuliah' => 'Pemrograman Web',
            'hari' => 'Kamis',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'warna' => '#3B82F6',
            'is_active' => true,
        ]);

        // Mock GoogleTokenService
        $tokenService = Mockery::mock(GoogleTokenService::class);
        $tokenService->shouldReceive('getAccessToken')->with(Mockery::any())->andReturn('fake-access-token');

        // Instantiate GoogleCalendarService
        $service = new GoogleCalendarService($tokenService);

        // Mock Guzzle Client
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('post')
            ->once()
            ->with(
                'https://www.googleapis.com/calendar/v3/calendars/primary/events',
                Mockery::on(function ($options) {
                    $json = $options['json'];
                    // Verify correct summary
                    if ($json['summary'] !== 'Pemrograman Web') {
                        return false;
                    }
                    // Verify Thursday is mapped properly to a thursday date
                    $startDateTime = $json['start']['dateTime'];
                    $endDateTime = $json['end']['dateTime'];

                    // Check that the date of the startDateTime is indeed a Thursday
                    $startDate = Carbon::parse(explode('T', $startDateTime)[0]);
                    if ($startDate->dayOfWeek !== Carbon::THURSDAY) {
                        return false;
                    }

                    // Verify correct formatted H:i:00 time
                    if (! str_ends_with($startDateTime, 'T08:00:00') || ! str_ends_with($endDateTime, 'T10:00:00')) {
                        return false;
                    }

                    return true;
                })
            )
            ->andReturn(new GuzzleResponse(200, [], json_encode(['id' => 'google-event-123'])));

        // Inject mockClient into service
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($service, $mockClient);

        $eventId = $service->exportCollegeSchedule($user, $schedule);

        $this->assertSame('google-event-123', $eventId);
        $schedule->refresh();
        $this->assertTrue($schedule->synced_to_calendar);
        $this->assertSame('google-event-123', $schedule->calendar_event_id);
    }
}
