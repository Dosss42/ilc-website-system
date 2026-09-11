<?php

return [

    // Full path to mysqldump/mysql executables. Left blank, BackupService
    // tries common XAMPP install locations, then falls back to the bare
    // command name (relying on PATH) — set this explicitly if a scheduled
    // Windows Task Scheduler run can't find them (it doesn't always
    // inherit the same PATH as a shell or XAMPP's own control panel).
    'mysqldump_path' => env('MYSQLDUMP_PATH'),
    'mysql_path'      => env('MYSQL_PATH'),

    // How many days of backups to keep in the primary (local) folder.
    // Ransomware-resilience note: this is deliberately NOT the only copy —
    // see 'secondary_path' and the Google Drive settings below. A local-only
    // backup sitting next to the app it protects offers no real protection
    // against an attack that compromises this machine.
    'keep_days' => env('BACKUP_KEEP_DAYS', 30),

    // A second local path (ideally a different physical/USB drive) that
    // every backup is also copied to. Left null/unset, this step is
    // skipped — no error, just no second copy.
    'secondary_path' => env('BACKUP_SECONDARY_PATH'),

    // How many days of backups to keep on the secondary drive — kept longer
    // than the primary copy by default, since it's the drive meant to
    // survive an incident on the main machine.
    'secondary_keep_days' => env('BACKUP_SECONDARY_KEEP_DAYS', 60),

    'google_drive' => [
        'enabled'          => env('BACKUP_GDRIVE_ENABLED', false),
        // Path to the downloaded service-account JSON key file.
        'credentials_path' => env('BACKUP_GDRIVE_CREDENTIALS_PATH', storage_path('app/google-drive-credentials.json')),
        // The target Drive folder's id (from its URL), shared with the
        // service account's email as Editor.
        'folder_id'        => env('BACKUP_GDRIVE_FOLDER_ID'),
        // Kept longest — this is the off-machine, ransomware-resilient copy.
        'keep_days'        => env('BACKUP_GDRIVE_KEEP_DAYS', 90),
    ],

];
