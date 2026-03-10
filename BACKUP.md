# MySQL backup (vcr_rt_rw)

## What it does

- Backs up the `vcr_rt_rw` database with `mysqldump`
- Saves files to `~/backup-vcr/` as `vcr_rt_rw_YYYY-MM-DD_HH-MM.sql`
- Reads DB credentials from this project’s `.env`
- Keeps only the last 7 days of backups and deletes older ones

## Run manually

From the project root:

```bash
./backup.sh
```

Or from anywhere (pass project root):

```bash
/path/to/vcr-rt-rw/backup.sh /path/to/vcr-rt-rw
```

## Cron (daily at 02:00)

1. Open your crontab:
   ```bash
   crontab -e
   ```

2. Add this line (adjust the path to your project):
   ```cron
   0 2 * * * /Users/user/Herd/vcr-rt-rw/backup.sh /Users/user/Herd/vcr-rt-rw >> /Users/user/backup-vcr/backup.log 2>&1
   ```

3. Save and exit (`:wq` in vim, or your editor’s save/exit).

Optional: create the log file and backup dir first:
```bash
mkdir -p ~/backup-vcr
touch ~/backup-vcr/backup.log
```

## Requirements

- `mysqldump` on `PATH` (Herd’s MySQL or system MySQL)
- `.env` in the project root with `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
