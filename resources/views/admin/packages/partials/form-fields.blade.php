@php
    /** @var \App\Models\InternetPackage|null $internetPackage */
    $internetPackage = $internetPackage ?? null;
@endphp

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <x-input-label for="code" value="Kode Paket" />
        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $internetPackage?->code)" required autofocus />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="name" value="Nama Paket" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $internetPackage?->name)" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="price" value="Harga (IDR)" />
        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price', $internetPackage?->price)" required />
        <x-input-error :messages="$errors->get('price')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="validity_value" value="Durasi" />
        <x-text-input id="validity_value" name="validity_value" type="number" min="1" class="mt-1 block w-full" :value="old('validity_value', $internetPackage?->validity_value)" required />
        <x-input-error :messages="$errors->get('validity_value')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="validity_unit" value="Satuan Durasi" />
        <select id="validity_unit" name="validity_unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rootPrimary focus:ring-rootTeal">
            @foreach (['hour' => 'Jam', 'day' => 'Hari', 'month' => 'Bulan'] as $unitValue => $unitLabel)
                <option value="{{ $unitValue }}" @selected(old('validity_unit', $internetPackage?->validity_unit) === $unitValue)>
                    {{ $unitLabel }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('validity_unit')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="quota_mb" value="Kuota (MB, opsional)" />
        <x-text-input id="quota_mb" name="quota_mb" type="number" min="1" class="mt-1 block w-full" :value="old('quota_mb', $internetPackage?->quota_mb)" />
        <x-input-error :messages="$errors->get('quota_mb')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="bandwidth_up_kbps" value="Upload (Kbps, opsional)" />
        <x-text-input id="bandwidth_up_kbps" name="bandwidth_up_kbps" type="number" min="1" class="mt-1 block w-full" :value="old('bandwidth_up_kbps', $internetPackage?->bandwidth_up_kbps)" />
        <x-input-error :messages="$errors->get('bandwidth_up_kbps')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="bandwidth_down_kbps" value="Download (Kbps, opsional)" />
        <x-text-input id="bandwidth_down_kbps" name="bandwidth_down_kbps" type="number" min="1" class="mt-1 block w-full" :value="old('bandwidth_down_kbps', $internetPackage?->bandwidth_down_kbps)" />
        <x-input-error :messages="$errors->get('bandwidth_down_kbps')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="mikrotik_profile" value="MikroTik Profile (opsional)" />
    <x-text-input id="mikrotik_profile" name="mikrotik_profile" type="text" class="mt-1 block w-full" :value="old('mikrotik_profile', $internetPackage?->mikrotik_profile)" />
    <x-input-error :messages="$errors->get('mikrotik_profile')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" value="Deskripsi (opsional)" />
    <textarea
        id="description"
        name="description"
        rows="4"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rootPrimary focus:ring-rootTeal"
    >{{ old('description', $internetPackage?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>
