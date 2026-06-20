<x-layouts.app title="Akademik & Analitik - Aviona Sync">
    <div x-data="{ 
        activeTab: '{{ request('tab', 'nilai') }}', 
        showAddModal: false 
    }" class="space-y-6">
        
        {{-- Header & Tab Section --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
            <nav class="-mb-px flex gap-6">
                <button @click="activeTab = 'nilai'; window.history.replaceState(null, null, '?tab=nilai')" 
                    :class="activeTab === 'nilai' ? 'border-pink-500 text-pink-600 font-bold dark:text-pink-400' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-semibold transition-all focus:outline-none">
                    Nilai & IPK
                </button>
                <button @click="activeTab = 'analitik'; window.history.replaceState(null, null, '?tab=analitik')" 
                    :class="activeTab === 'analitik' ? 'border-pink-500 text-pink-600 font-bold dark:text-pink-400' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-semibold transition-all focus:outline-none">
                    Analitik Progres
                </button>
            </nav>
        </div>

        {{-- TAB 1: Nilai & IPK --}}
        <div x-show="activeTab === 'nilai'" x-transition class="space-y-6">
            {{-- Title Section --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Tracker Akademik</h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola nilai perkuliahan, hitung IPS & IPK secara otomatis dengan skala standar A=4.0 s/d E=0.0.</p>
                </div>
                <x-button @click="showAddModal = true" class="bg-pink-600 hover:bg-pink-700 dark:bg-pink-700 dark:hover:bg-pink-600 focus:ring-pink-100">
                    Tambah Nilai
                </x-button>
            </div>

            {{-- Statistics Overview --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <x-card class="bg-gradient-to-br from-pink-500 to-rose-600 text-white border-0 shadow-lg dark:from-pink-600 dark:to-rose-700">
                    <p class="text-sm font-medium text-pink-100">IPK Kumulatif</p>
                    <p class="mt-2 text-4xl font-extrabold text-white">{{ number_format($ipk, 2) }}</p>
                    <p class="mt-2 text-xs text-pink-100/80">
                        Predikat: 
                        @if ($ipk >= 3.51)
                            Pujian (Cum Laude)
                        @elseif ($ipk >= 3.0)
                            Sangat Memuaskan
                        @elseif ($ipk >= 2.0)
                            Memuaskan
                        @elseif ($ipk >= 1.0)
                            Cukup
                        @else
                            -
                        @endif
                    </p>
                </x-card>

                <x-card>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.58 0 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.58 0-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Total SKS Lulus</p>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $cumulativeSks }} SKS</p>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Jumlah Semester</p>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ count($semesterStats) }} Semester</p>
                        </div>
                    </div>
                </x-card>
            </div>

            {{-- Line Chart Card --}}
            <x-card>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Perkembangan IP Semester (IPS)</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Grafik tren nilai indeks prestasi dari semester ke semester.</p>

                <div class="mt-6">
                    @if (count($chartPoints) > 0)
                        <div class="relative w-full overflow-hidden">
                            <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-auto max-h-64" style="overflow: visible;">
                                <defs>
                                    <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#ec4899" stop-opacity="0.25" />
                                        <stop offset="100%" stop-color="#ec4899" stop-opacity="0.0" />
                                    </linearGradient>
                                </defs>

                                {{-- Y Axis Guidelines --}}
                                @for ($g = 0; $g <= 4; $g++)
                                    @php
                                        $guideY = ($chartHeight - $chartPadding) - ($g * (($chartHeight - 2 * $chartPadding) / 4));
                                    @endphp
                                    <line x1="{{ $chartPadding }}" y1="{{ $guideY }}" x2="{{ $chartWidth - $chartPadding }}" y2="{{ $guideY }}" class="stroke-slate-100 dark:stroke-slate-800" stroke-width="1" stroke-dasharray="4" />
                                    <text x="{{ $chartPadding - 8 }}" y="{{ $guideY + 4 }}" text-anchor="end" class="text-[10px] font-medium fill-slate-400 dark:fill-slate-500 font-sans">{{ number_format($g, 1) }}</text>
                                @endfor

                                {{-- Area under the line --}}
                                @if ($fillPath)
                                    <path d="{{ $fillPath }}" fill="url(#chartGradient)" />
                                @endif

                                {{-- The main line --}}
                                @if ($linePath)
                                    <path d="{{ $linePath }}" fill="none" stroke="#db2777" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                @endif

                                {{-- Data Points (Circles and Labels) --}}
                                @foreach ($chartPoints as $point)
                                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="5" class="fill-white dark:fill-slate-900" stroke="#db2777" stroke-width="2.5" />
                                    
                                    {{-- IPS Value Tag --}}
                                    <text x="{{ $point['x'] }}" y="{{ $point['y'] - 10 }}" text-anchor="middle" class="text-[11px] font-bold fill-pink-700 dark:fill-pink-400 font-sans">{{ number_format($point['ips'], 2) }}</text>
                                    
                                    {{-- Semester Label on X axis --}}
                                    <text x="{{ $point['x'] }}" y="{{ $chartHeight - $chartPadding + 18 }}" text-anchor="middle" class="text-[10px] font-semibold fill-slate-500 dark:fill-slate-400 font-sans">Sem {{ $point['semester'] }}</text>
                                @endforeach
                            </svg>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-400 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <h4 class="mt-4 font-semibold text-slate-700 dark:text-slate-300">Grafik belum tersedia</h4>
                            <p class="mt-1 text-sm text-slate-400 dark:text-slate-500 max-w-sm">Masukkan nilai mata kuliah terlebih dahulu untuk melihat perkembangan IP semester Anda.</p>
                        </div>
                    @endif
                </div>
            </x-card>

            {{-- Semester Details --}}
            <div class="space-y-6">
                @forelse ($semesterStats as $semNum => $sem)
                    <x-card class="overflow-hidden p-0 border-slate-100 dark:border-slate-700">
                        {{-- Semester Summary Header --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50/75 dark:bg-slate-800/50 px-5 py-4 border-b border-slate-100 dark:border-slate-700/60">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Semester {{ $semNum }}</h3>
                                <span class="rounded-full bg-slate-200/60 dark:bg-slate-700/60 px-2.5 py-0.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                    {{ $sem['total_sks'] }} SKS
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-slate-500 dark:text-slate-400">IP Semester:</span>
                                <span class="rounded-xl bg-pink-50 dark:bg-pink-900/30 px-3 py-1 text-base font-extrabold text-pink-700 dark:text-pink-400">
                                    {{ number_format($sem['ips'], 2) }}
                                </span>
                            </div>
                        </div>

                        {{-- Course Tables --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                        <th class="px-6 py-3 font-semibold">Mata Kuliah</th>
                                        <th class="px-6 py-3 font-semibold text-center w-24">SKS</th>
                                        <th class="px-6 py-3 font-semibold text-center w-24">Nilai</th>
                                        <th class="px-6 py-3 font-semibold text-center w-28">Bobot</th>
                                        <th class="px-6 py-3 text-right w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50 bg-white dark:bg-slate-800">
                                    @foreach ($sem['grades'] as $grade)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition">
                                            <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white break-words">{{ $grade->mata_kuliah }}</td>
                                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 text-center font-medium">{{ $grade->sks }}</td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <span class="rounded-full px-2.5 py-0.5 text-xs font-bold bg-pink-50 dark:bg-pink-900/30 text-pink-700 dark:text-pink-400">
                                                    {{ $grade->nilai }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 text-center font-medium">{{ number_format($grade->grade_point, 1) }}</td>
                                            <td class="px-6 py-4 text-right">
                                                <x-modal message="Apakah Anda yakin ingin menghapus nilai mata kuliah ini?">
                                                    <x-slot:trigger>
                                                        <button type="button" class="inline-flex items-center text-xs font-semibold text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition">Hapus</button>
                                                    </x-slot:trigger>
                                                    <form method="POST" action="{{ route('academic-tracker.destroy', $grade) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-button variant="danger" type="submit">Ya, Hapus</x-button>
                                                    </form>
                                                </x-modal>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                @empty
                    <x-empty-state title="Belum ada data akademik" description="Mulailah dengan menambahkan mata kuliah dan nilaimu per semester.">
                        <x-button @click="showAddModal = true" class="bg-pink-600 hover:bg-pink-700 dark:bg-pink-700 dark:hover:bg-pink-600">Tambah Nilai</x-button>
                    </x-empty-state>
                @endforelse
            </div>
        </div>

        {{-- TAB 2: Analitik --}}
        <div x-show="activeTab === 'analitik'" x-transition class="space-y-6" style="display: none;">
            {{-- Title Section --}}
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Analitik Progres</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pantau progres penyelesaian tugas dan statistik aktivitas belajarmu.</p>
            </div>

            {{-- Filters Form --}}
            <form method="GET" action="{{ route('academic-tracker.index') }}" class="flex flex-wrap items-end gap-3 rounded-3xl border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-4">
                <input type="hidden" name="tab" value="analitik">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Dari Tanggal</label>
                    <x-input name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Sampai Tanggal</label>
                    <x-input name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Mata Kuliah</label>
                    <x-select name="course">
                        <option value="">Semua</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course }}" {{ ($filters['course'] ?? '') === $course ? 'selected' : '' }}>{{ $course }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Kategori</label>
                    <x-select name="category">
                        <option value="">Semua</option>
                        @foreach (['tugas','kuliah','uts','uas','organisasi'] as $cat)
                            <option value="{{ $cat }}" {{ ($filters['category'] ?? '') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Sumber</label>
                    <x-select name="source">
                        <option value="">Semua</option>
                        @foreach (['local','classroom','calendar'] as $src)
                            <option value="{{ $src }}" {{ ($filters['source'] ?? '') === $src ? 'selected' : '' }}>{{ ucfirst($src) }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-button type="submit" variant="secondary">Filter</x-button>
                </div>
            </form>

            {{-- Analytics Stats Overview Grid --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <x-card>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Tugas</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">{{ $total }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Selesai</p>
                    <p class="mt-2 text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $completed }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Dikerjakan</p>
                    <p class="mt-2 text-3xl font-extrabold text-blue-600 dark:text-blue-400">{{ $in_progress }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Terlambat</p>
                    <p class="mt-2 text-3xl font-extrabold text-red-600 dark:text-red-400">{{ $overdue }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Progres</p>
                    <p class="mt-2 text-3xl font-extrabold text-pink-600 dark:text-pink-400">{{ $percentage }}%</p>
                    <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                        <div class="h-full rounded-full bg-pink-500" style="width: {{ $percentage }}%"></div>
                    </div>
                </x-card>
            </div>

            {{-- Weekly Trend and Upcoming Tasks --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <x-card>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Tren Mingguan</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Jumlah tugas yang diselesaikan 7 hari terakhir.</p>
                        <div class="mt-6 grid grid-cols-7 gap-2">
                            @foreach ($weekly_trend as $day)
                                <div class="text-center">
                                    <div class="mx-auto mb-2 flex h-24 items-end justify-center">
                                        <div class="w-8 rounded-t-lg bg-pink-500 dark:bg-pink-600 transition-all" style="height: {{ min(($day['completed'] * 20) + 4, 96) }}px"></div>
                                    </div>
                                    <p class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $day['day'] }}</p>
                                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1">{{ $day['completed'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </x-card>
                </div>
                
                <div>
                    @if ($upcoming->isNotEmpty())
                        <x-card class="h-full">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Tugas Mendatang</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Agenda akademik berikutnya.</p>
                            <div class="space-y-3">
                                @foreach ($upcoming as $task)
                                    <div class="flex items-center gap-3 rounded-2xl bg-pink-50/20 dark:bg-pink-950/10 p-3 border border-pink-100/30 dark:border-pink-900/20">
                                        <span class="h-2.5 w-2.5 rounded-full bg-pink-500"></span>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[120px]">{{ $task->judul }}</span>
                                        <span class="ml-auto text-xs font-medium text-slate-400 dark:text-slate-500">{{ $task->waktu_pelaksanaan->format('d/m/Y') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    @endif
                </div>
            </div>
        </div>

        {{-- Add Modal for Academic Grade --}}
        <div x-show="showAddModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4" style="display:none;">
            <div @click.away="showAddModal = false" class="w-full max-w-md rounded-3xl bg-white dark:bg-slate-800 p-6 shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-transparent dark:border-slate-700">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Nilai Mata Kuliah</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('academic-tracker.store') }}" class="mt-4 space-y-4">
                    @csrf
                    
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Semester</label>
                        <x-select name="semester" required>
                            @for ($s = 1; $s <= 10; $s++)
                                <option value="{{ $s }}">Semester {{ $s }}</option>
                            @endfor
                        </x-select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Nama Mata Kuliah</label>
                        <x-input name="mata_kuliah" placeholder="Contoh: Algoritma & Struktur Data" required />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Jumlah SKS</label>
                            <x-select name="sks" required>
                                @for ($k = 1; $k <= 6; $k++)
                                    <option value="{{ $k }}" @selected($k === 3)>{{ $k }} SKS</option>
                                @endfor
                            </x-select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Nilai Huruf</label>
                            <x-select name="nilai" required>
                                <option value="A">A (4.0)</option>
                                <option value="AB">AB (3.5)</option>
                                <option value="B">B (3.0)</option>
                                <option value="BC">BC (2.5)</option>
                                <option value="C">C (2.0)</option>
                                <option value="D">D (1.0)</option>
                                <option value="E">E (0.0)</option>
                            </x-select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="showAddModal = false" class="inline-flex items-center justify-center rounded-2xl border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-700">Batal</button>
                        <x-button type="submit" class="bg-pink-600 hover:bg-pink-700 dark:bg-pink-700 dark:hover:bg-pink-600 focus:ring-pink-100">Simpan Nilai</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
