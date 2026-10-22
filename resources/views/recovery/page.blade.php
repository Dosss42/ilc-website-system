<!DOCTYPE html>
<html lang="en">
<head>
    {{-- Emergency Recovery — no session, no CSRF token, no login (see EmergencyRecoveryController):
         every form carries the Recovery Key or a short-lived token signed with it. --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Recovery — IEMELIF Learning Center</title>
    <meta name="robots" content="noindex, nofollow">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px; background: #0d2147; }
        .bg-image { position: fixed; inset: 0; background: url('/images/bg.png') center/cover no-repeat; filter: brightness(0.32) blur(3px); z-index: 0; }
        .wrap { position: relative; z-index: 1; width: 100%; max-width: {{ $unlocked ? '760px' : '440px' }}; }
        .card { background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 32px 80px rgba(0,0,0,0.55); }
        .head { background: linear-gradient(135deg, #7f1d1d 0%, #b91c1c 60%, #dc2626 100%); padding: 28px 32px 24px; text-align: center; }
        .logo { width: 64px; height: 64px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; overflow: hidden; }
        .logo img { width: 92%; height: 92%; object-fit: contain; }
        .head h2 { font-size: 16px; font-weight: 800; color: #fff; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 4px; }
        .head p { font-size: 11.5px; color: rgba(255,255,255,0.75); }
        .body { padding: 28px 32px 32px; }
        label { font-size: 12px; font-weight: 700; color: #374151; display: block; margin-bottom: 6px; }
        input[type=text], input[type=email], input[type=password], input[type=file], select {
            width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 11px 14px; font-size: 13px;
            font-family: 'Poppins', sans-serif; background: #f9fafb; color: #111; margin-bottom: 14px;
        }
        input:focus, select:focus { border-color: #b91c1c; outline: none; background: #fff; }
        .key-input { letter-spacing: 2px; font-family: monospace !important; font-size: 15px !important; text-transform: uppercase; }
        .btn { width: 100%; border: none; border-radius: 10px; padding: 12px; font-size: 13.5px; font-weight: 700; font-family: 'Poppins', sans-serif;
               cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; color: #fff; background: linear-gradient(135deg, #1a3a6c, #1e5799); }
        .btn.red { background: linear-gradient(135deg, #b91c1c, #dc2626); }
        .btn.grey { background: #e5e7eb; color: #374151; }
        .msg { border-radius: 10px; padding: 12px 14px; font-size: 12.5px; margin-bottom: 18px; line-height: 1.55; }
        .msg.error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .msg.ok { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .msg.info { background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; }
        .msg.warn { background: #fff8ec; border: 1px solid #fde68a; color: #92400e; }
        .step { border: 1px solid #e5e7eb; border-radius: 14px; padding: 18px 20px; margin-bottom: 16px; }
        .step h3 { font-size: 14px; font-weight: 700; color: #1a3a6c; margin-bottom: 4px; }
        .step .sub { font-size: 12px; color: #6b7280; margin-bottom: 14px; line-height: 1.5; }
        .row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .or { text-align: center; font-size: 11px; color: #9ca3af; margin: 4px 0 12px; text-transform: uppercase; letter-spacing: 1px; }
        .check { display: flex; gap: 8px; align-items: flex-start; font-size: 12px; color: #374151; margin-bottom: 12px; font-weight: 500; }
        .check input { margin-top: 3px; }
        .password { font-family: monospace; font-size: 20px; font-weight: 700; letter-spacing: 2px; color: #1a3a6c; background: #f0f7ff;
                    border: 2px dashed #1e5799; border-radius: 10px; padding: 12px; text-align: center; margin: 10px 0; user-select: all; }
        .foot { text-align: center; margin-top: 16px; font-size: 12px; color: #6b7280; }
        .foot a { color: #1a3a6c; font-weight: 700; text-decoration: none; }
        @media (max-width: 600px) { .row2 { grid-template-columns: 1fr; } .body { padding: 22px 20px 26px; } }
    </style>
</head>
<body>
<div class="bg-image"></div>
<div class="wrap">
    <div class="card">
        <div class="head">
            <div class="logo"><img src="/images/logo.png" alt="ILC" onerror="this.style.display='none'"></div>
            <h2><i class="bi bi-shield-lock-fill"></i> Emergency Recovery</h2>
            <p>For when no one can log in to the school system</p>
        </div>
        <div class="body">

            @isset($error)
                <div class="msg error"><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</div>
            @endisset

            @if(!$setUp)
                <div class="msg info">
                    <i class="bi bi-info-circle-fill"></i>
                    Emergency Recovery is <strong>not set up</strong> on this server (no Recovery Key).
                    Ask whoever manages the system to set one up — see <em>docs/EMERGENCY_RECOVERY.md</em>.
                </div>
                <div class="foot"><a href="/login"><i class="bi bi-arrow-left"></i> Back to login</a></div>

            @elseif(!$unlocked)
                <div class="msg warn">
                    Use this only if <strong>nobody can log in</strong> (for example the Super Admin account was deleted or the data is damaged).
                    Every attempt is recorded and emailed to the Super Admins.
                </div>
                <form method="POST" action="{{ route('recovery.unlock') }}" autocomplete="off">
                    <label for="recovery_key">Recovery Key (from the sealed envelope)</label>
                    <input type="password" class="key-input" id="recovery_key" name="recovery_key" placeholder="XXXX-XXXX-XXXX-XXXX-XXXX-XXXX" required autofocus>
                    <button type="submit" class="btn red"><i class="bi bi-unlock-fill"></i> Unlock</button>
                </form>
                <div class="foot">Forgot only the password? Use <a href="{{ route('password.request') }}">Forgot password</a> instead.</div>

            @else
                <div class="msg {{ $database['ok'] ? 'info' : 'error' }}">
                    <i class="bi bi-database{{ $database['ok'] ? '-check' : '-x' }}"></i> {{ $database['text'] }}
                    This recovery session lasts {{ \App\Services\EmergencyRecovery::TOKEN_MINUTES }} minutes per step.
                </div>

                @isset($restored)
                    <div class="msg ok">
                        <i class="bi bi-check-circle-fill"></i> <strong>Database restored</strong> from {{ $restored['label'] }}.
                        @if($restored['safety'])
                            The data from before was saved as <strong>{{ \App\Services\BackupService::displayName($restored['safety']) }}</strong> — restore it to undo.
                        @else
                            (No safety copy could be made — the database was too damaged to save.)
                        @endif
                        If nobody can log in with the restored data, continue with step 2.
                    </div>
                @endisset

                @isset($account)
                    <div class="msg ok">
                        <i class="bi bi-check-circle-fill"></i>
                        <strong>Super Admin {{ $account['created'] ? 'created' : 'repaired' }}:</strong> {{ $account['email'] }}
                        @if($account['fixed']) ({{ implode(', ', $account['fixed']) }}) @endif
                        <div style="margin-top:8px;">Temporary password — write it down now, it is shown only once:</div>
                        <div class="password">{{ $account['password'] }}</div>
                        <a href="/login" style="color:#166534;font-weight:700;">Log in now</a> and change this password right away.
                    </div>
                @endisset

                {{-- Step 1: restore data --}}
                <div class="step">
                    <h3><i class="bi bi-1-circle-fill"></i> Restore the data from a backup</h3>
                    <div class="sub">Only if the data is wrong or damaged. Everything is replaced with the backup; the current data is saved first as a safety copy when possible.</div>

                    <form method="POST" action="{{ route('recovery.restore-upload') }}" enctype="multipart/form-data">
                        <input type="hidden" name="token" value="{{ $token }}">
                        <label for="backup_file">Upload a backup file you downloaded (.sql)</label>
                        <input type="file" id="backup_file" name="backup_file" accept=".sql" required>
                        <label class="check"><input type="checkbox" required> I understand all current data will be replaced by this backup.</label>
                        <button type="submit" class="btn red"><i class="bi bi-upload"></i> Upload &amp; Restore</button>
                    </form>

                    @if(count($backups))
                        <div class="or">or a backup already on the server</div>
                        <form method="POST" action="{{ route('recovery.restore-server') }}">
                            <input type="hidden" name="token" value="{{ $token }}">
                            <select name="file" required>
                                @foreach($backups as $bk)
                                    <option value="{{ $bk['name'] }}">{{ $bk['label'] }} ({{ $bk['size'] }})</option>
                                @endforeach
                            </select>
                            <label class="check"><input type="checkbox" required> I understand all current data will be replaced by this backup.</label>
                            <button type="submit" class="btn red"><i class="bi bi-arrow-counterclockwise"></i> Restore this backup</button>
                        </form>
                    @endif
                </div>

                {{-- Step 2: get a Super Admin login back --}}
                <div class="step">
                    <h3><i class="bi bi-2-circle-fill"></i> Get the Super Admin login back</h3>
                    <div class="sub">Brings back the account if it was archived, makes it Super Admin, reactivates it, clears a lockout and gives a new temporary password. Creates the account if it doesn't exist.</div>
                    <form method="POST" action="{{ route('recovery.superadmin') }}" autocomplete="off">
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="row2">
                            <div>
                                <label for="email">Super Admin email</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div>
                                <label for="name">Name (only if it has to be created)</label>
                                <input type="text" id="name" name="name" value="Super Admin">
                            </div>
                        </div>
                        <button type="submit" class="btn"><i class="bi bi-person-check-fill"></i> Repair Super Admin account</button>
                    </form>
                </div>

                <a href="{{ route('recovery.show') }}" class="btn grey" style="text-decoration:none;"><i class="bi bi-lock-fill"></i> Finish &amp; lock</a>
            @endif
        </div>
    </div>
</div>
</body>
</html>
