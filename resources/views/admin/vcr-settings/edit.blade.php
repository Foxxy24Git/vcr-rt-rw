<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            VCR Settings
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">Terdapat kesalahan validasi:</p>
                    <ul class="mt-1 list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-xl bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.vcr-settings.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="length" value="Panjang Kode / Password" />
                        <x-text-input
                            id="length"
                            name="length"
                            type="number"
                            min="4"
                            max="32"
                            class="mt-1 block w-full"
                            :value="old('length', $setting->length)"
                            required
                        />
                        <x-input-error :messages="$errors->get('length')" class="mt-2" />
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-2">
                            <input type="checkbox" name="allow_uppercase" value="1" class="rounded border-gray-300 text-rootPrimary focus:ring-rootTeal" @checked(old('allow_uppercase', $setting->allow_uppercase))>
                            <span class="text-sm text-gray-700">Izinkan huruf besar (A-Z)</span>
                        </label>

                        <label class="flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-2">
                            <input type="checkbox" name="allow_lowercase" value="1" class="rounded border-gray-300 text-rootPrimary focus:ring-rootTeal" @checked(old('allow_lowercase', $setting->allow_lowercase))>
                            <span class="text-sm text-gray-700">Izinkan huruf kecil (a-z)</span>
                        </label>

                        <label class="flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-2">
                            <input type="checkbox" name="allow_numbers" value="1" class="rounded border-gray-300 text-rootPrimary focus:ring-rootTeal" @checked(old('allow_numbers', $setting->allow_numbers))>
                            <span class="text-sm text-gray-700">Izinkan angka (0-9)</span>
                        </label>

                        <label class="flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-2">
                            <input type="checkbox" name="user_equals_password" value="1" class="rounded border-gray-300 text-rootPrimary focus:ring-rootTeal" @checked(old('user_equals_password', $setting->user_equals_password))>
                            <span class="text-sm text-gray-700">Username sama dengan Password</span>
                        </label>
                    </div>

                    <p class="text-xs text-gray-500">
                        Format username dan password disimpan sebagai template internal sistem. Saat ini default: username mengikuti kode voucher, password random sesuai konfigurasi.
                    </p>

                    <div class="flex items-center justify-end gap-2">
                        <button type="submit" class="rounded-xl bg-rootPrimary px-4 py-2 text-sm font-medium text-white hover:bg-rootIndigo">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>
