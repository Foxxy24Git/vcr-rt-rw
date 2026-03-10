<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Tambah Reseller
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-4 sm:px-6 lg:px-8">
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
                <form method="POST" action="{{ route('admin.resellers.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Nama Reseller" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="phone" value="No. Telepon" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="password" value="Password" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                        </div>
                    </div>

                    <p class="text-xs text-gray-500">
                        Role akan otomatis diset sebagai <strong>reseller</strong> dan status default <strong>aktif</strong>.
                    </p>

                    <div class="pt-2 flex items-center justify-end gap-2">
                        <a href="{{ route('admin.resellers.index') }}" class="rounded-xl border border-rootPrimary px-4 py-2 text-sm text-rootPrimary hover:bg-rootPink/20">
                            Batal
                        </a>
                        <button type="submit" class="rounded-xl bg-rootPrimary px-4 py-2 text-sm font-medium text-white hover:bg-rootIndigo">
                            Simpan Reseller
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>
