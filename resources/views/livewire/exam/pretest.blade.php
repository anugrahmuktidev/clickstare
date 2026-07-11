<div class="w-full max-w-3xl mx-auto bg-white rounded-lg shadow p-6 space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Pretest</h1>
        <p class="mt-1 text-sm text-slate-600">Isi pertanyaan awal berikut sebelum masuk ke bagian pengetahuan.</p>
    </div>

    <form wire:submit.prevent="submit" class="space-y-6">
        <div class="rounded-lg border border-slate-200 p-4 space-y-3">
            <p class="font-semibold text-slate-800">Uang Saku</p>
            <div class="space-y-2">
                @foreach ($pocketMoneyOptions as $value => $label)
                    <label class="flex items-center gap-3 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700">
                        <input type="radio" wire:model="pocket_money_range" value="{{ $value }}" class="h-4 w-4">
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('pocket_money_range')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-lg border border-slate-200 p-4 space-y-3">
            <p class="font-semibold text-slate-800">Apakah Anda merokok elektrik?</p>
            <div class="grid gap-2 sm:grid-cols-2">
                <label class="flex items-center gap-3 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700">
                    <input type="radio" wire:model="uses_electric_smoke" value="ya" class="h-4 w-4">
                    <span>Ya</span>
                </label>
                <label class="flex items-center gap-3 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700">
                    <input type="radio" wire:model="uses_electric_smoke" value="tidak" class="h-4 w-4">
                    <span>Tidak</span>
                </label>
            </div>
            @error('uses_electric_smoke')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-lg border border-slate-200 p-4 space-y-3">
            <p class="font-semibold text-slate-800">Apakah Anda merokok tembakau/konvensional?</p>
            <div class="grid gap-2 sm:grid-cols-2">
                <label class="flex items-center gap-3 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700">
                    <input type="radio" wire:model="uses_conventional_smoke" value="ya" class="h-4 w-4">
                    <span>Ya</span>
                </label>
                <label class="flex items-center gap-3 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700">
                    <input type="radio" wire:model="uses_conventional_smoke" value="tidak" class="h-4 w-4">
                    <span>Tidak</span>
                </label>
            </div>
            @error('uses_conventional_smoke')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-lg border border-slate-200 p-4 space-y-3">
            <p class="font-semibold text-slate-800">Apakah Anda merokok elektrik dan konvensional/tembakau?</p>
            <div class="grid gap-2 sm:grid-cols-2">
                <label class="flex items-center gap-3 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700">
                    <input type="radio" wire:model="uses_both_smoke_types" value="ya" class="h-4 w-4">
                    <span>Ya</span>
                </label>
                <label class="flex items-center gap-3 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700">
                    <input type="radio" wire:model="uses_both_smoke_types" value="tidak" class="h-4 w-4">
                    <span>Tidak</span>
                </label>
            </div>
            @error('uses_both_smoke_types')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
                Mulai Bagian Pengetahuan
            </button>
        </div>
    </form>
</div>
