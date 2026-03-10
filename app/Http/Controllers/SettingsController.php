<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Daftar key setting yang boleh diubah dari form.
     *
     * @var list<string>
     */
    private const ALLOWED_KEYS = [
        'mikrotik_host',
        'mikrotik_port',
        'mikrotik_timeout',
        'hotspot_name',
    ];

    /**
     * Tampilkan halaman pengaturan sistem.
     */
    public function index(): View
    {
        $settings = Setting::query()->pluck('value', 'key');

        return view('settings.index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Simpan pengaturan dari form.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->except('_token');
        $rules = $this->validationRules($data);

        $validated = $request->validate($rules);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value === null || $value === '' ? null : $value);
        }

        return redirect()->back()->with('status', __('Settings saved successfully.'));
    }

    /**
     * Buat aturan validasi untuk key yang dikirim.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, list<string>>
     */
    private function validationRules(array $data): array
    {
        $rules = [];

        foreach (array_keys($data) as $key) {
            if (! in_array($key, self::ALLOWED_KEYS, true)) {
                continue;
            }

            $rules[$key] = match ($key) {
                'mikrotik_port', 'mikrotik_timeout' => ['nullable', 'integer', 'min:1', 'max:65535'],
                default => ['nullable', 'string', 'max:255'],
            };
        }

        return $rules;
    }
}
