<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Kuliah - Aviona Sync</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #334155;
            margin: 0;
            padding: 10px;
            font-size: 13px;
            background-color: #ffffff;
        }
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #fda4af; /* Rose 300 accent */
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 5px 0;
        }
        .header p {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }
        .day-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .day-title {
            font-size: 15px;
            font-weight: bold;
            color: #db2777; /* Pink 600 */
            border-bottom: 1px solid #fbcfe8; /* Pink 200 */
            padding-bottom: 5px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .schedule-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-align: left;
            padding: 8px 12px;
            font-size: 11px;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
        }
        .schedule-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .time-col {
            width: 15%;
            font-weight: bold;
            color: #0f172a;
        }
        .subject-col {
            width: 40%;
        }
        .subject-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .meta-text {
            font-size: 11px;
            color: #64748b;
        }
        .dosen-col {
            width: 25%;
            color: #475569;
        }
        .room-col {
            width: 20%;
            color: #475569;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Jadwal Kuliah</h1>
        <p>Aviona Sync &bull; Dicetak pada {{ now()->translatedFormat('l, d F Y H.i') }}</p>
    </div>

    @foreach ($days as $day)
        @if ($grouped->has($day))
            <div class="day-section">
                <div class="day-title">{{ $day }}</div>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th class="time-col">Waktu</th>
                            <th class="subject-col">Mata Kuliah</th>
                            <th class="dosen-col">Dosen</th>
                            <th class="room-col">Ruangan / Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grouped[$day] as $schedule)
                            <tr>
                                <td class="time-col">
                                    <div style="border-left: 3px solid {{ $schedule->warna ?? '#db2777' }}; padding-left: 6px;">
                                        {{ $schedule->jam_mulai }} - {{ $schedule->jam_selesai }}
                                    </div>
                                </td>
                                <td class="subject-col">
                                    <div class="subject-name">{{ $schedule->mata_kuliah }}</div>
                                    @if($schedule->catatan)
                                        <div class="meta-text" style="font-style: italic; margin-top: 2px;">
                                            Catatan: {{ $schedule->catatan }}
                                        </div>
                                    @endif
                                </td>
                                <td class="dosen-col">
                                    {{ $schedule->dosen ?: '-' }}
                                </td>
                                <td class="room-col">
                                    {{ $schedule->lokasi ?: '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh Aviona Sync pada {{ now()->format('Y') }}.
    </div>

</body>
</html>
