<x-layouts.app title="Akademik - Aviona Sync">
    <div x-data="{ showAddModal: false }">
        {{-- Header Section --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Tracker Akademik</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola nilai perkuliahan, hitung IPS & IPK secara otomatis.</p>
            </div>
            <x-button @click="showAddModal = true" class="bg-pink-600 hover:bg-pink-700 focus:ring-pink-100">
                ✨ Tambah Nilai
            </x-button>
        </div>

        {{-- Statistics Overview --}}
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-card class="bg-gradient-to-br from-pink-500 to-rose-600 text-white border-0 shadow-lg">
                <p class="text-sm font-medium text-pink-100">IPK Kumulatif</p>
                <p class="mt-2 text-4xl font-extrabold">{{ number_format($ipk, 2) }}</p>
                <p class="mt-2 text-xs text-pink-100/80">
                    Predikat: 
                    @if ($ipk >= 3.51)
                        Pujian (Cum Laude) 👑
                    @elseif ($ipk >= 3.0)
                        Sangat Memuaskan 🌟
                    @elseif ($ipk >= 2.0)
                        Memuaskan 👍
                    @elseif ($ipk >= 1.0)
                        Cukup
                    @else
                        -
                    @endif
                </p>
            </x-card>

            <x-card>
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.58 0 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.58 0-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Total SKS Lulus</p>
                        <p class="text-2xl font-bold text-slate-900">{{ $cumulativeSks }} SKS</p>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-pink-50 text-pink-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Jumlah Semester</p>
                        <p class="text-2xl font-bold text-slate-900">{{ count($semesterStats) }} Semester</p>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Line Chart Card --}}
        <div class="mt-6">
            <x-card>
                <h3 class="text-lg font-semibold text-slate-900">Perkembangan IP Semester (IPS)</h3>
                <p class="text-sm text-slate-500">Grafik tren nilai indeks prestasi dari semester ke semester.</p>

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
                                    <line x1="{{ $chartPadding }}" y1="{{ $guideY }}" x2="{{ $chartWidth - $chartPadding }}" y2="{{ $guideY }}" class="stroke-slate-100" stroke-width="1" stroke-dasharray="4" />
                                    <text x="{{ $chartPadding - 8 }}" y="{{ $guideY + 4 }}" text-anchor="end" class="text-[10px] font-medium fill-slate-400 font-sans">{{ number_format($g, 1) }}</text>
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
                                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="5" fill="#ffffff" stroke="#db2777" stroke-width="2.5" class="transition hover:r-6 cursor-pointer" />
                                    
                                    {{-- IPS Value Tag --}}
                                    <text x="{{ $point['x'] }}" y="{{ $point['y'] - 10 }}" text-anchor="middle" class="text-[11px] font-bold fill-pink-700 font-sans bg-white">{{ number_format($point['ips'], 2) }}</text>
                                    
                                    {{-- Semester Label on X axis --}}
                                    <text x="{{ $point['x'] }}" y="{{ $chartHeight - $chartPadding + 18 }}" text-anchor="middle" class="text-[10px] font-semibold fill-slate-500 font-sans">Sem {{ $point['semester'] }}</text>
                                @endforeach
                            </svg>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <span class="text-4xl">📈</span>
                            <h4 class="mt-4 font-semibold text-slate-700">Grafik belum tersedia</h4>
                            <p class="mt-1 text-sm text-slate-400 max-w-sm">Masukkan nilai mata kuliah terlebih dahulu untuk melihat perkembangan IP semester Anda.</p>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>

        {{-- Semester Details --}}
        <div class="mt-6 space-y-6">
            @forelse ($semesterStats as $semNum => $sem)
                <x-card class="overflow-hidden p-0 border-slate-100">
                    {{-- Semester Summary Header --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50/75 px-5 py-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-bold text-slate-800">Semester {{ $semNum }}</h3>
                            <span class="rounded-full bg-slate-200/60 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                                {{ $sem['total_sks'] }} SKS
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-slate-500">IP Semester:</span>
                            <span class="rounded-xl bg-pink-50 px-3 py-1 text-base font-extrabold text-pink-700">
                                {{ number_format($sem['ips'], 2) }}
                            </span>
                        </div>
                    </div>

                    {{-- Course Tables --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-white text-xs font-bold uppercase tracking-wider text-slate-400">
                                    <th class="px-6 py-3 font-semibold">Mata Kuliah</th>
                                    <th class="px-6 py-3 font-semibold text-center w-24">SKS</th>
                                    <th class="px-6 py-3 font-semibold text-center w-24">Nilai</th>
                                    <th class="px-6 py-3 font-semibold text-center w-28">Bobot</th>
                                    <th class="px-6 py-3 text-right w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 bg-white">
                                @foreach ($sem['grades'] as $grade)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="px-6 py-4 text-sm font-medium text-slate-900 break-words">{{ $grade->mata_kuliah }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-600 text-center font-medium">{{ $grade->sks }}</td>
                                        <td class="px-6 py-4 text-sm text-center">
                                            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold bg-pink-50 text-pink-700">
                                                {{ $grade->nilai }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 text-center font-medium">{{ number_format($grade->grade_point, 1) }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <x-modal message="Apakah Anda yakin ingin menghapus nilai mata kuliah ini?">
                                                <x-slot:trigger>
                                                    <button type="button" class="inline-flex items-center text-xs font-semibold text-red-600 hover:text-red-700 transition">Hapus</button>
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
                    <x-button @click="showAddModal = true" class="bg-pink-600 hover:bg-pink-700">✨ Tambah Nilai</x-button>
                </x-empty-state>
            @endforelse
        </div>

        {{-- Add Modal (Custom Modal with Alpine) --}}
        <div x-show="showAddModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4" style="display:none;">
            <div @click.away="showAddModal = false" class="w-full max-w-md rounded-3xl bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.12)]">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-lg font-bold text-slate-900">✨ Tambah Nilai Mata Kuliah</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form method="POST" action="{{ route('academic-tracker.store') }}" class="mt-4 space-y-4">
                    @csrf
                    
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Semester</label>
                        <x-select name="semester" required>
                            @for ($s = 1; $s <= 10; $s++)
                                <option value="{{ $s }}">Semester {{ $s }}</option>
                            @endfor
                        </x-select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Mata Kuliah</label>
                        <x-input name="mata_kuliah" placeholder="Contoh: Algoritma & Struktur Data" required />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Jumlah SKS</label>
                            <x-select name="sks" required>
                                @for ($k = 1; $k <= 6; $k++)
                                    <option value="{{ $k }}" @selected($k === 3)>{{ $k }} SKS</option>
                                @endfor
                            </x-select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Nilai Huruf</label>
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

                    <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="showAddModal = false" class="inline-flex items-center justify-center rounded-2xl border border-slate-100 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</button>
                        <x-button type="submit" class="bg-pink-600 hover:bg-pink-700 focus:ring-pink-100">Simpan Nilai</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
