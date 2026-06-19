@if (session('success'))
    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 px-4 py-3 text-sm font-medium text-emerald-700">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="rounded-2xl bg-red-50 border border-red-100 px-4 py-3 text-sm font-medium text-red-700">
        {{ session('error') }}
    </div>
@endif
