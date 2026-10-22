<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Traits\HandlesBackups;

/**
 * Admin › Backup & Restore — create, download, restore from the list and Restore from
 * File, through the same code as the Super Admin page (HandlesBackups). Deleting backups
 * is Super Admin only, so the automatic safety copies can't be erased from here.
 */
class BackupController extends Controller
{
    use HandlesBackups;

    /** Fragment for the on-demand Backup & Restore section (GET admin.section.backup). */
    public function section()
    {
        $drive = config('backup.google_drive');

        return view('admin.sections.backup-content', [
            'backups'        => $this->listBackups(),
            'driveConnected' => !empty($drive['enabled']) && !empty($drive['folder_id'])
                && (!empty($drive['refresh_token']) || is_file((string) ($drive['credentials_path'] ?? ''))),
        ]);
    }
}
