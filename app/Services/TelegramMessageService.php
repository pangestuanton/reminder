<?php

namespace App\Services;

use App\Models\CollegeSchedule;
use App\Models\GoogleCalendarEvent;
use App\Models\JadwalKegiatan;
use App\Models\User;

class TelegramMessageService
{
    protected array $lastTemplates = [];

    public function buildReminderMessage(JadwalKegiatan $jadwal, string $reminderType, ?int $minutesRemaining = null): string
    {
        $category = $this->detectCategory($jadwal);
        $stage = $this->determineStage($reminderType, $minutesRemaining);

        return $this->renderTemplate($category, $stage, $jadwal, $minutesRemaining);
    }

    public function buildClassroomMessage(JadwalKegiatan $jadwal, string $reminderType, ?int $minutesRemaining = null): string
    {
        $lines = [];

        if ($reminderType === 'h3') {
            $lines[] = $this->pickTemplate('classroom_h3_prepare', $jadwal);
        } elseif ($reminderType === 'h1') {
            $lines[] = $this->pickTemplate('classroom_h1_prepare', $jadwal);
        } else {
            $lines[] = $this->pickTemplate('classroom_3h_urgent', $jadwal, $minutesRemaining);
        }

        $lines[] = '';
        $lines[] = "📚 *{$this->escapeMarkdown($jadwal->course_name)}*";

        if ($jadwal->course_name) {
            $lines[] = "Mata Kuliah: {$this->escapeMarkdown($jadwal->course_name)}";
        }

        $deadline = $jadwal->getEffectiveDeadline();
        $lines[] = "Deadline: {$deadline->translatedFormat('l, d F Y \\- H.i')}";
        $lines[] = "Status: {$this->getStatusLabel($jadwal)}";
        $lines[] = "Sumber: Google Classroom";

        return implode("\n", $lines);
    }

    public function buildCollegeClassMessage(CollegeSchedule $schedule): string
    {
        $dayMap = [
            'Senin' => 'Senin', 'Selasa' => 'Selasa', 'Rabu' => 'Rabu',
            'Kamis' => 'Kamis', 'Jumat' => 'Jumat', 'Sabtu' => 'Sabtu',
            'Minggu' => 'Minggu',
        ];

        $day = $dayMap[$schedule->hari] ?? $schedule->hari;
        $lines = [
            "📅 *Kuliah Hari Ini*",
            '',
            "📘 {$this->escapeMarkdown($schedule->mata_kuliah)}",
        ];

        if ($schedule->dosen) {
            $lines[] = "👨‍🏫 Dosen: {$this->escapeMarkdown($schedule->dosen)}";
        }

        $lines[] = "🕐 Jam: {$schedule->jam_mulai} \\- {$schedule->jam_selesai}";
        $lines[] = "📍 {$this->escapeMarkdown($schedule->lokasi ?? 'Belum ditentukan')}";

        if ($schedule->catatan) {
            $lines[] = "📝 {$this->escapeMarkdown($schedule->catatan)}";
        }

        return implode("\n", $lines);
    }

    public function buildCalendarEventMessage(GoogleCalendarEvent $event): string
    {
        $lines = [
            "📅 *Acara Kalender*",
            '',
            "📌 {$this->escapeMarkdown($event->title)}",
        ];

        if ($event->is_all_day) {
            $lines[] = "🕐 Sepanjang hari ({$event->start_date->format('d/m/Y')})";
        } else {
            $lines[] = "🕐 {$event->start_datetime->format('H.i')} \\- {$event->end_datetime->format('H.i')}";
        }

        if ($event->location) {
            $lines[] = "📍 {$this->escapeMarkdown($event->location)}";
        }

        $lines[] = "Sumber: Google Calendar";

        return implode("\n", $lines);
    }

    public function buildOverdueMessage(JadwalKegiatan $jadwal): string
    {
        $category = $this->detectCategory($jadwal);

        return match ($category) {
            'examination', 'quiz' => $this->pickTemplate('overdue_exam', $jadwal),
            'presentation' => $this->pickTemplate('overdue_presentation', $jadwal),
            'practicum' => $this->pickTemplate('overdue_practicum', $jadwal),
            'group_project' => $this->pickTemplate('overdue_group', $jadwal),
            default => $this->pickTemplate('overdue_default', $jadwal),
        };
    }

    protected function detectCategory(JadwalKegiatan $jadwal): string
    {
        $title = strtolower($jadwal->judul);
        $desc = strtolower($jadwal->deskripsi ?? '');
        $combined = $title . ' ' . $desc;

        if (str_contains($combined, 'uts') || str_contains($combined, 'uas') || str_contains($combined, 'ulangan') || str_contains($combined, 'exam')) {
            return 'examination';
        }
        if (str_contains($combined, 'quiz') || str_contains($combined, 'kuis')) {
            return 'quiz';
        }
        if (str_contains($combined, 'praktikum') || str_contains($combined, 'lab')) {
            return 'practicum';
        }
        if (str_contains($combined, 'presentasi') || str_contains($combined, 'presentation')) {
            return 'presentation';
        }
        if (str_contains($combined, 'kelompok') || str_contains($combined, 'group') || str_contains($combined, 'tim')) {
            return 'group_project';
        }
        if (str_contains($combined, 'kuliah') || str_contains($combined, 'class') || str_contains($combined, 'mata kuliah')) {
            return 'college_class';
        }
        if (str_contains($combined, 'organisasi') || str_contains($combined, 'meeting')) {
            return 'organization';
        }

        return 'assignment';
    }

    protected function determineStage(string $reminderType, ?int $minutesRemaining): string
    {
        return match (true) {
            $reminderType === 'h3' => 'h3',
            $reminderType === 'h1' => 'h1',
            $minutesRemaining !== null && $minutesRemaining <= 180 => '3h',
            $reminderType === 'overdue' => 'overdue',
            default => '3h',
        };
    }

    protected function renderTemplate(string $category, string $stage, JadwalKegiatan $jadwal, ?int $minutesRemaining): string
    {
        $key = "{$category}_{$stage}";
        $template = $this->pickTemplate($key, $jadwal, $minutesRemaining);

        $lines = explode("\n", $template);
        $lines[] = '';
        $lines[] = "📌 {$this->escapeMarkdown($jadwal->judul)}";

        if ($jadwal->course_name) {
            $lines[] = "📚 {$this->escapeMarkdown($jadwal->course_name)}";
        }

        $deadline = $jadwal->getEffectiveDeadline();
        $lines[] = "🕐 {$deadline->translatedFormat('l, d F Y \\- H.i')}";

        if ($jadwal->lokasi_atau_link) {
            if (filter_var($jadwal->lokasi_atau_link, FILTER_VALIDATE_URL)) {
                $lines[] = "🔗 Link: {$this->escapeMarkdown($jadwal->lokasi_atau_link)}";
            } else {
                $lines[] = "📍 {$this->escapeMarkdown($jadwal->lokasi_atau_link)}";
            }
        }

        $lines[] = "⚠️ Prioritas: " . ucfirst($jadwal->prioritas);
        $lines[] = "Sumber: {$jadwal->source_label}";

        if ($minutesRemaining !== null && $minutesRemaining > 0) {
            $remaining = $this->formatRemainingTime($minutesRemaining);
            $lines[] = "⏳ Tersisa: {$remaining}";
        }

        return implode("\n", $lines);
    }

    protected function pickTemplate(string $key, JadwalKegiatan $jadwal, ?int $minutesRemaining = null): string
    {
        $title = $this->escapeMarkdown($jadwal->judul);
        $remaining = $minutesRemaining ? $this->formatRemainingTime($minutesRemaining) : '';

        $templates = match ($key) {
            'assignment_h3' => [
                "Hai! 👋 Tugas ini sudah dekat nih. Siapkan semuanya mulai sekarang biar nggak kewalahan!",
                "Jangan tunda ya! Tugas ini tinggal 3 hari lagi. Mulai kerjakan sekarang! 💪",
                "Yuk mulai persiapan! Tenggat tinggal 3 hari. Kamu pasti bisa! 🚀",
            ],
            'assignment_h1' => [
                "Besok sudah deadline nih! ⏰ Sudah siapkan semuanya?",
                "Tinggal 1 hari lagi! Ayo selesaikan sekarang supaya tenang. 🎯",
                "Besok tenggat! Kalau belum selesai, ayo kerjakan malam ini. 💫",
            ],
            'assignment_3h' => [
                "⏰ {$remaining} lagi! Sudah siap? Semangat ya!",
                "Waktu tinggal {$remaining}. Ayo selesaikan sekarang! 🏃‍♂️",
                "Tinggal {$remaining}. Kamu hampir sampai! 🎉",
            ],
            'quiz_h3' => [
                "Kuis sudah dekat! 📖 Mulai review materi sekarang ya!",
                "3 hari lagi kuis! Siapkan catatan dan materi belajarmu. 📚",
                "Yuk mulai belajar untuk kuis minggu depan! 📝",
            ],
            'quiz_h1' => [
                "Besok kuis! 📖 Sudah review materi?",
                "Kuis tinggal besok! Siapkan catatanmu. 🎯",
                "Tinggal 1 hari untuk konsentrasi belajar! 🧠",
            ],
            'quiz_3h' => [
                "⏰ Kuis {$remaining} lagi! Review sekilas ya!",
                "Tinggal {$remaining} menjelang kuis. Semangat! 📖",
                "Kuis segera dimulai! Stay focused! 🎯",
            ],
            'examination_h3' => [
                "Ujian sudah dekat! 📚 Mulai belajar dari sekarang.",
                "3 hari lagi ujian. Siapkan strategi belajarmu! 🎯",
                "Ujian menanti! Yuk mulai persiapan intensif. 💪",
            ],
            'examination_h1' => [
                "Besok ujian! 📖 Sudah siap?",
                "Tinggal 1 hari! Review materi utama ya. 🧠",
                "Ujian tinggal besok. Istirahat yang cukup malam ini! 💤",
            ],
            'examination_3h' => [
                "⏰ Ujian {$remaining} lagi! Stay calm dan fokus!",
                "Tinggal {$remaining}. Kamu sudah belajar, sekarang saatnya percaya diri! 💪",
                "Ujian segera. Tarik napas, kamu pasti bisa! 🌟",
            ],
            'practicum_h3' => [
                "Praktikum sudah dekat! 🔬 Siapkan laporan dan alatmu.",
                "3 hari lagi praktikum. Persiapkan segala kebutuhannya! 🧪",
                "Yuk siapkan praktikum minggu ini! 📋",
            ],
            'practicum_h1' => [
                "Besok praktikum! 🔬 Sudah siap?",
                "Praktikum tinggal besok. Cek lagi persiapanmu ya. 📋",
                "Tinggal 1 hari! Siapkan alat dan catatan praktikum. 🧪",
            ],
            'practicum_3h' => [
                "⏰ Praktikum {$remaining} lagi! Sudah siap?",
                "Tinggal {$remaining} menjelang praktikum. Semangat! 🔬",
                "Praktikum segera. Yuk cek persiapan terakhir! 📋",
            ],
            'presentation_h3' => [
                "Presentasi sudah dekat! 🎤 Mulai persiapkan slide dan materi.",
                "3 hari lagi presentasi. Siapkan dirimu! 📊",
                "Yuk latihan presentasi dari sekarang! 🎙️",
            ],
            'presentation_h1' => [
                "Besok presentasi! 🎤 Sudah siap?",
                "Presentasi tinggal besok. Latihan sebentar ya! 🎯",
                "Tinggal 1 hari! Percaya diri, kamu pasti hebat! 🌟",
            ],
            'presentation_3h' => [
                "⏰ Presentasi {$remaining} lagi! Semangat!",
                "Tinggal {$remaining}. Kamu sudah siap! 🎤",
                "Presentasi segera dimulai. Stay confident! 💫",
            ],
            'group_project_h3' => [
                "Deadline proyek kelompok sudah dekat! 🤝 Koordinasi dengan tim ya.",
                "3 hari lagi deadline kelompok. Pastikan semua bagian selesai! 👥",
                "Yuk selesaikan proyek kelompok bersama! 💪",
            ],
            'group_project_h1' => [
                "Besok deadline kelompok! 🤝 Sudah koordinasi?",
                "Tinggal 1 hari. Pastikan semua kontribusi sudah masuk! 👥",
                "Deadline kelompok tinggal besok. Semangat kerja sama! 🎯",
            ],
            'group_project_3h' => [
                "⏰ Deadline kelompok {$remaining} lagi! Cek kontribusimu!",
                "Tinggal {$remaining}. Timmu butuh kamu! 🤝",
                "Kelompok segera deadline. Finalisasi sekarang! 🎯",
            ],
            'overdue_exam' => [
                "⚠️ Tenggat ujian sudah terlewat!",
                "Ujian sudah lewat waktu. Segera hubungi dosen. ⚠️",
            ],
            'overdue_presentation' => [
                "⚠️ Presentasi sudah terlewat!",
                "Deadline presentasi sudah lewat. 📋",
            ],
            'overdue_practicum' => [
                "⚠️ Laporan praktikum sudah terlewat!",
                "Deadline praktikum sudah lewat. 🔬",
            ],
            'overdue_group' => [
                "⚠️ Deadline proyek kelompok sudah terlewat!",
                "Kelompok sudah melewati tenggat. 🤝",
            ],
            'overdue_default' => [
                "⚠️ Tenggat sudah terlewat!",
                "Kegiatan ini sudah melewati deadline. ⏰",
            ],
            default => [
                "Pengingat untuk: {$title}",
                "Jangan lupa persiapkan kegiatan ini ya!",
            ],
        };

        $selected = $templates[array_rand($templates)];

        if (! in_array($key, $this->lastTemplates, true)) {
            $this->lastTemplates[] = $key;
            if (count($this->lastTemplates) > 10) {
                array_shift($this->lastTemplates);
            }

            return $selected;
        }

        $index = array_search($key, array_keys($templates));
        $altIndex = ($index + 1) % count($templates);

        return $templates[$altIndex] ?? $selected;
    }

    protected function getStatusLabel(JadwalKegiatan $jadwal): string
    {
        if ($jadwal->isOverdue()) {
            return 'Terlambat';
        }

        return match ($jadwal->status) {
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => 'Menunggu',
        };
    }

    protected function formatRemainingTime(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours} jam {$mins} menit";
        }
        if ($hours > 0) {
            return "{$hours} jam";
        }

        return "{$mins} menit";
    }

    public function escapeMarkdown(string $text): string
    {
        $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];

        foreach ($specialChars as $char) {
            $text = str_replace($char, '\\' . $char, $text);
        }

        return $text;
    }
}
