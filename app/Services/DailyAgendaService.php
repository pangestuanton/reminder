<?php

namespace App\Services;

use App\Models\CollegeSchedule;
use App\Models\GoogleCalendarEvent;
use App\Models\JadwalKegiatan;
use App\Models\NotificationLog;
use App\Models\User;
use App\Notifications\DailyAgendaNotification;
use Illuminate\Support\Facades\Log;

class DailyAgendaService
{
    protected TelegramMessageService $messageService;

    public function __construct(TelegramMessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function buildAgendaMessage(User $user): ?string
    {
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        $tasks = JadwalKegiatan::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('deadline_at', '>=', now()->startOfDay())
                    ->orWhere('waktu_pelaksanaan', '>=', now()->startOfDay())
                    ->where('waktu_pelaksanaan', '<=', now()->endOfDay());
            })
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('waktu_pelaksanaan')
            ->get();

        $collegeClasses = CollegeSchedule::where('user_id', $user->id)
            ->active()
            ->currentSemester()
            ->forDay($this->getDayName(now()->dayOfWeek))
            ->orderBy('jam_mulai')
            ->get();

        $calendarEvents = GoogleCalendarEvent::where('user_id', $user->id)
            ->forDate(now())
            ->orderBy('start_datetime')
            ->get();

        $overdue = JadwalKegiatan::where('user_id', $user->id)
            ->pending()
            ->overdue()
            ->orderBy('waktu_pelaksanaan')
            ->get();

        if ($tasks->isEmpty() && $collegeClasses->isEmpty() && $calendarEvents->isEmpty() && ($overdue->isEmpty() || ! $user->daily_agenda_include_overdue)) {
            if (! $user->daily_agenda_enabled) {
                return null;
            }

            return $this->buildEmptyDayMessage();
        }

        $lines = $this->buildHeader($today);
        $lines[] = '';

        $totalItems = 0;
        $completedItems = 0;

        if ($collegeClasses->isNotEmpty()) {
            $lines[] = '📚 *KULIAH HARI INI*';
            $lines[] = '───────────────';
            foreach ($collegeClasses as $class) {
                $totalItems++;
                $lines[] = "• {$class->jam_mulai}\\-{$class->jam_selesai} {$this->messageService->escapeMarkdown($class->mata_kuliah)}";
                if ($class->dosen) {
                    $lines[] = "  👨‍🏫 {$this->messageService->escapeMarkdown($class->dosen)}";
                }
                $lines[] = "  📍 {$this->messageService->escapeMarkdown($class->lokasi ?? '-')}";
                $lines[] = '';
            }
        }

        if ($tasks->isNotEmpty()) {
            $grouped = $tasks->groupBy('kategori');

            $categoryOrder = ['tugas', 'uts', 'uas', 'kuliah', 'organisasi'];
            $categoryLabels = [
                'tugas' => '📝 TUGAS & DEADLINE',
                'uts' => '📋 UTS',
                'uas' => '📋 UAS',
                'kuliah' => '🎓 AKADEMIK',
                'organisasi' => '🤝 ORGANISASI',
            ];

            foreach ($categoryOrder as $cat) {
                if (! $grouped->has($cat)) {
                    continue;
                }

                $lines[] = $categoryLabels[$cat] ?? strtoupper($cat);
                $lines[] = '───────────────';

                foreach ($grouped[$cat] as $task) {
                    $totalItems++;
                    if ($task->status === 'selesai') {
                        $completedItems++;
                    }

                    $status = $task->status === 'selesai' ? '✅' : ($task->isOverdue() ? '🔴' : '⏳');
                    $time = $task->waktu_pelaksanaan->format('H.i');
                    $source = $task->source !== 'local' ? " [{$task->source_label}]" : '';

                    $lines[] = "{$status} {$time} {$this->messageService->escapeMarkdown($task->judul)}{$source}";
                    if ($task->course_name) {
                        $lines[] = "  📚 {$this->messageService->escapeMarkdown($task->course_name)}";
                    }
                    $lines[] = '';
                }
            }
        }

        if ($calendarEvents->isNotEmpty()) {
            $lines[] = '📅 *ACARA KALENDER*';
            $lines[] = '───────────────';
            foreach ($calendarEvents as $event) {
                $totalItems++;
                $time = $event->is_all_day ? 'Sepanjang hari' : $event->start_datetime->format('H.i');
                $lines[] = "• {$time} {$this->messageService->escapeMarkdown($event->title)}";
                $lines[] = '';
            }
        }

        if ($user->daily_agenda_include_overdue && $overdue->isNotEmpty()) {
            $lines[] = '🔴 *TUGAS TERLAMBAT*';
            $lines[] = '───────────────';
            foreach ($overdue as $task) {
                $totalItems++;
                $daysOverdue = abs(now()->diffInDays($task->waktu_pelaksanaan, false));
                $lines[] = "• {$this->messageService->escapeMarkdown($task->judul)} ({$daysOverdue} hari terlambat)";
                $lines[] = '';
            }
        }

        $remaining = $totalItems - $completedItems;
        $lines[] = '───────────────';
        $lines[] = "📊 Total: {$totalItems} | Selesai: {$completedItems} | Tersisa: {$remaining}";
        $lines[] = '';
        $lines[] = $this->buildClosingMotivation();

        $message = implode("\n", $lines);

        if (mb_strlen($message) > 4000) {
            $message = mb_substr($message, 0, 3950) . "\n\n... 📋 agenda terpotong. Cek dashboard untuk detail lengkap.";
        }

        return $message;
    }

    protected function buildHeader(string $date): array
    {
        $dayName = now()->translatedFormat('l');
        $greetings = [
            'Selamat pagi! ☀️',
            'Pagi yang cerah! 🌤️',
            'Selamat pagi, semangat baru! 🌟',
        ];

        $closingMessages = [
            'Semangat hari ini! Kamu pasti bisa menyelesaikan semuanya! 💪',
            'Hari yang produktif menanti. Ayo mulai! 🚀',
            'Jangan lupa istirahat yang cukup ya! 🌙',
            'Kamu sudah jauh melangkah. Pertahankan! 🌟',
            'Setiap hari adalah kesempatan baru. Semangat! ☀️',
            'Kerja kerasmu akan membuah hasil. Keep going! 🎯',
            'Jangan lupa makan dan minum yang cukup! 🍎',
        ];

        return [
            $greetings[array_rand($greetings)] . ' 👋',
            "📅 *Agenda {$dayName}, {$date}*",
        ];
    }

    protected function buildClosingMotivation(): string
    {
        $messages = [
            'Semangat hari ini! Kamu pasti bisa menyelesaikan semuanya! 💪',
            'Hari yang produktif menanti. Ayo mulai! 🚀',
            'Jangan lupa istirahat yang cukup ya! 🌙',
            'Kamu sudah jauh melangkah. Pertahankan! 🌟',
            'Setiap hari adalah kesempatan baru. Semangat! ☀️',
        ];

        return $messages[array_rand($messages)];
    }

    protected function buildEmptyDayMessage(): string
    {
        $messages = [
            "🎉 *Hari ini kosong!* Nikmati waktu luangmu. Tapi jangan lupa cek jadwal besok ya!",
            "🌿 *Hari bebas!* Manfaatkan untuk istirahat atau mengejar tugas yang tertunda.",
            "✨ *Tidak ada agenda hari ini.* Ini saat yang tepat untuk rehat sejenak!",
        ];

        return $messages[array_rand($messages)];
    }

    protected function getDayName(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Senin',
        };
    }

    public function shouldSend(User $user): bool
    {
        if (! $user->daily_agenda_enabled) {
            return false;
        }

        if (! $user->telegram_chat_id) {
            return false;
        }

        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $uniqueKey = "daily_agenda_{$today}";

        return ! NotificationLog::where('user_id', $user->id)
            ->where('unique_key', $uniqueKey)
            ->exists();
    }

    public function logSent(User $user): void
    {
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        NotificationLog::create([
            'user_id' => $user->id,
            'notification_type' => 'daily_agenda',
            'category' => 'daily_agenda',
            'unique_key' => "daily_agenda_{$today}",
            'channel' => 'telegram',
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
