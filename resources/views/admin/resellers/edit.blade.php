<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Edit Reseller: {{ $reseller->name }}
        </h2>
    </x-slot>

    <div class="py-4 sm:py-6 md:py-8">
        <div class="mx-auto max-w-4xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">Terdapat kesalahan input:</p>
                    <ul class="mt-1 list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900">Profil Reseller</h3>

                <form method="POST" action="{{ route('admin.resellers.update', $reseller->id) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Nama Reseller" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $reseller->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $reseller->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="phone" value="No. Telepon" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $reseller->phone)" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rootPrimary focus:ring-rootTeal" required>
                            <option value="active" @selected(old('status', $reseller->status) === 'active')>Aktif</option>
                            <option value="inactive" @selected(old('status', $reseller->status) === 'inactive')>Nonaktif</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-600" for="discount_percent">
                            Diskon Reseller (%)
                        </label>
                        <input
                            id="discount_percent"
                            type="number"
                            name="discount_percent"
                            min="0"
                            max="100"
                            value="{{ old('discount_percent', $reseller->discount_percent ?? 0) }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-rootPrimary focus:ring-rootTeal"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            Contoh: 10 berarti reseller mendapat diskon 10%.
                        </p>
                        <x-input-error :messages="$errors->get('discount_percent')" class="mt-2" />
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2">
                        <a href="{{ route('admin.resellers.index') }}" class="rounded-xl border border-rootPrimary px-4 py-2 text-sm text-rootPrimary hover:bg-rootPink/20">
                            Kembali
                        </a>
                        <button type="submit" class="rounded-xl bg-rootPrimary px-4 py-2 text-sm font-medium text-white hover:bg-rootIndigo">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900">Reset Password Reseller</h3>

                <form method="POST" action="{{ route('admin.resellers.reset-password', $reseller->id) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="password" value="Password Baru" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-md bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-500">
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>
