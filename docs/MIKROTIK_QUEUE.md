# MikroTik Queue Jobs

## Queue configuration

The app uses the **database** queue driver by default (`QUEUE_CONNECTION=database` in `.env`). Jobs are stored in the `jobs` table; when a job fails after all retries, it is moved to the `failed_jobs` table.

Ensure the migration has been run so `jobs` and `failed_jobs` tables exist:

```bash
php artisan migrate
```

Run the queue worker:

```bash
php artisan queue:work database
```

## CreateHotspotUserJob

Creates a single hotspot user on the MikroTik router via `MikrotikService::createHotspotUser()`. Uses the database connection, retries up to 3 times, and logs all final failures.

### Example usage

**Dispatch from code:**

```php
use App\Jobs\CreateHotspotUserJob;

// Dispatch immediately (async)
CreateHotspotUserJob::dispatch('john', 'secret123', 'HS-1D');

// Dispatch to a specific queue
CreateHotspotUserJob::dispatch('jane', 'pass456', 'HS-1H')->onQueue('mikrotik');

// Delay execution
CreateHotspotUserJob::dispatch('bob', 'pw789', 'HS-1M')->delay(now()->addMinutes(5));
```

**From a controller or service:**

```php
public function provisionUser(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'username' => 'required|string|max:255',
        'password' => 'required|string|min:6',
        'profile'  => 'required|string|max:100',
    ]);

    CreateHotspotUserJob::dispatch(
        $validated['username'],
        $validated['password'],
        $validated['profile']
    );

    return back()->with('status', 'Hotspot user creation queued.');
}
```

## Failed jobs

Failed jobs (after all retries) are stored in the `failed_jobs` table and can be managed via Artisan or the admin UI.

### Retry failed jobs

Retry all failed jobs:

```bash
php artisan queue:retry all
```

Retry a specific job by ID:

```bash
php artisan queue:retry <job-id>
```

### Prune old failed jobs

Remove failed jobs older than 7 days (default):

```bash
php artisan queue:prune-failed
```

Custom retention (e.g. 14 days):

```bash
php artisan queue:prune-failed --days=14
```

Pruning is scheduled to run daily (7-day retention) via `routes/console.php`. Ensure the scheduler is running:

```bash
php artisan schedule:work
```
