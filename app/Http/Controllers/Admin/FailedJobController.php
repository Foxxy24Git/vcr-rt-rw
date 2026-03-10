<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class FailedJobController extends Controller
{
    public function index(): View
    {
        $failedJobs = DB::table('failed_jobs')
            ->select(['id', 'uuid', 'connection', 'queue', 'exception', 'failed_at'])
            ->orderByDesc('failed_at')
            ->paginate(15);

        return view('admin.monitoring.failed-jobs.index', [
            'failedJobs' => $failedJobs,
        ]);
    }

    public function retry(int $failedJob): RedirectResponse
    {
        $job = DB::table('failed_jobs')
            ->select(['id', 'uuid'])
            ->where('id', $failedJob)
            ->first();

        if (! $job) {
            return redirect()
                ->route('admin.failed-jobs.index')
                ->with('error', 'Failed job tidak ditemukan.');
        }

        try {
            $exitCode = Artisan::call('queue:retry', [
                'id' => [$job->uuid],
            ]);

            if ($exitCode !== 0) {
                return redirect()
                    ->route('admin.failed-jobs.index')
                    ->with('error', trim(Artisan::output()) ?: 'Gagal menjalankan retry job.');
            }
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.failed-jobs.index')
                ->with('error', 'Terjadi error saat retry failed job.');
        }

        return redirect()
            ->route('admin.failed-jobs.index')
            ->with('status', "Failed job #{$failedJob} berhasil diretry.");
    }
}
