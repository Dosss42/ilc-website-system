    <div id="section-settings" class="dash-section" style="display:none;">

        
        <div class="stg-hero">
            <div class="stg-hero-icon"><i class="bi bi-gear-fill"></i></div>
            <div>
                <h1 class="stg-hero-title">Settings</h1>
                <p class="stg-hero-sub">Manage school information, academic, financial, and system settings.</p>
            </div>
        </div>

        
        <div class="stg-layout">

            
            <div class="stg-nav">
                <button class="stg-nav-item active" id="stab-btn-school" onclick="showSettingsTab('school')">
                    <div class="stg-nav-icon"><i class="bi bi-building"></i></div>
                    <div class="stg-nav-text">
                        <div class="stg-nav-label">School Info</div>
                        <div class="stg-nav-desc">Name, address, contact</div>
                    </div>
                    <i class="bi bi-chevron-right stg-nav-caret"></i>
                </button>
                <button class="stg-nav-item" id="stab-btn-academic" onclick="showSettingsTab('academic')">
                    <div class="stg-nav-icon"><i class="bi bi-mortarboard"></i></div>
                    <div class="stg-nav-text">
                        <div class="stg-nav-label">Academic</div>
                        <div class="stg-nav-desc">School year, grading, terms</div>
                    </div>
                    <i class="bi bi-chevron-right stg-nav-caret"></i>
                </button>
                <button class="stg-nav-item" id="stab-btn-security" onclick="showSettingsTab('security')">
                    <div class="stg-nav-icon"><i class="bi bi-shield-lock"></i></div>
                    <div class="stg-nav-text">
                        <div class="stg-nav-label">Security</div>
                        <div class="stg-nav-desc">Auth, sessions, passwords</div>
                    </div>
                    <i class="bi bi-chevron-right stg-nav-caret"></i>
                </button>
                <button class="stg-nav-item" id="stab-btn-account" onclick="showSettingsTab('account')">
                    <div class="stg-nav-icon"><i class="bi bi-person-circle"></i></div>
                    <div class="stg-nav-text">
                        <div class="stg-nav-label">My Account</div>
                        <div class="stg-nav-desc">Profile, photo, password</div>
                    </div>
                    <i class="bi bi-chevron-right stg-nav-caret"></i>
                </button>
            </div>

            
            <div class="stg-content">

                
                <div class="content-card settings-tab" id="tab-school">
                    <div class="content-card-header" style="border-left:4px solid var(--blue);padding-left:16px;">
                        <div>
                            <h6 style="margin:0;"><i class="bi bi-building me-2" style="color:var(--blue);"></i>School Information</h6>
                            <div style="font-size:11px;color:var(--muted);margin-top:2px;">Changes here will reflect across the entire system</div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="stg-section-label">Identity</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">School Name</label>
                                <input type="text" id="set-school_name" class="form-fld" placeholder="Enter school name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-lbl">Principal / Director Name</label>
                                <input type="text" id="set-principal_name" class="form-fld" placeholder="Enter principal name">
                            </div>
                            <div class="col-md-12">
                                <label class="form-lbl">School Motto / Vision</label>
                                <input type="text" id="set-school_motto" class="form-fld" placeholder="Enter school motto or vision statement">
                            </div>
                        </div>
                        <div class="stg-section-label">Contact &amp; Location</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">School Address</label>
                                <input type="text" id="set-school_address" class="form-fld" placeholder="Enter school address">
                            </div>
                            <div class="col-md-3">
                                <label class="form-lbl">Phone Number</label>
                                <input type="text" id="set-school_phone" class="form-fld" placeholder="e.g. 0912-345-6789">
                            </div>
                            <div class="col-md-3">
                                <label class="form-lbl">Email Address</label>
                                <input type="email" id="set-school_email" class="form-fld" placeholder="e.g. info@school.edu">
                            </div>
                        </div>
                        <div class="stg-section-label">Branding</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-lbl">School Logo Path</label>
                                <input type="text" id="set-school_logo" class="form-fld" placeholder="/images/logo.png">
                                <small class="text-muted" style="font-size:11px;">Path to logo image in the public folder</small>
                            </div>
                        </div>
                        <div class="stg-section-label">GCash Payment Details</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-lbl">GCash Number</label>
                                <input type="text" id="set-gcash_number" class="form-fld" placeholder="e.g. 0917 123 4567">
                                <small class="text-muted" style="font-size:11px;">Shown to students when paying via GCash</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-lbl">GCash Account Name</label>
                                <input type="text" id="set-gcash_account_name" class="form-fld" placeholder="e.g. IEMELIF Learning Center">
                            </div>
                            <div class="col-md-4">
                                <label class="form-lbl">GCash QR Code Path</label>
                                <input type="text" id="set-gcash_qr_path" class="form-fld" placeholder="qrcodes/gcash.png">
                                <small class="text-muted" style="font-size:11px;">Relative path inside <code>storage/</code></small>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button class="btn-dash btn-primary" onclick="saveSettings('school')">
                                <i class="bi bi-floppy-fill me-1"></i> Save School Information
                            </button>
                        </div>
                    </div>
                </div>

                
                <div class="content-card settings-tab" id="tab-academic" style="display:none;">
                    <div class="content-card-header" style="border-left:4px solid #f59e0b;padding-left:16px;">
                        <div>
                            <h6 style="margin:0;"><i class="bi bi-mortarboard me-2" style="color:#f59e0b;"></i>Academic Settings</h6>
                            <div style="font-size:11px;color:var(--muted);margin-top:2px;">Configure grading system and academic year</div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="stg-section-label">School Year</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-lbl">Current School Year</label>
                                <input type="text" id="set-current_school_year" class="form-fld" placeholder="e.g. 2026-2027">
                            </div>
                            <div class="col-md-4">
                                <label class="form-lbl">School Year Start</label>
                                <input type="date" id="set-school_year_start" class="form-fld">
                            </div>
                            <div class="col-md-4">
                                <label class="form-lbl">School Year End</label>
                                <input type="date" id="set-school_year_end" class="form-fld">
                            </div>
                        </div>
                        <div class="stg-section-label">System Controls</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-lbl">Total Terms</label>
                                <select id="set-total_terms" class="form-fld">
                                    <option value="3" selected>3 Terms</option>
                                    <option value="4">4 Quarters</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-lbl">Enrollment Status</label>
                                <select id="set-enrollment_open" class="form-fld">
                                    <option value="1">Open</option>
                                    <option value="0">Closed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-lbl">Enrollment Target School Year</label>
                                <?php
                                    $baseY = now()->month >= 6 ? now()->year : now()->year - 1;
                                    $syOptions = [];
                                    for ($i = 0; $i <= 3; $i++) {
                                        $syOptions[] = ($baseY + $i) . '-' . ($baseY + $i + 1);
                                    }
                                ?>
                                <select id="set-enrollment_target_year" class="form-fld">
                                    <option value="">— Auto (next school year) —</option>
                                    <?php $__currentLoopData = $syOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($sy); ?>"><?php echo e($sy); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small class="text-muted" style="font-size:11px;">The year students are enrolling INTO. Used for re-enrollment.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-lbl">Maintenance Mode</label>
                                <select id="set-maintenance_mode" class="form-fld" onchange="toggleMaintenanceMode()">
                                    <option value="0" <?php echo e(!($maintenanceMode ?? false) ? 'selected' : ''); ?>>Off — Portals accessible</option>
                                    <option value="1" <?php echo e(($maintenanceMode ?? false) ? 'selected' : ''); ?>>On — Portals blocked</option>
                                </select>
                                <small class="text-muted" style="font-size:11px;">Blocks student &amp; teacher portals. Admins always have access.</small>
                            </div>
                        </div>
                        <div class="stg-section-label">Grading</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-lbl">Passing Grade</label>
                                <input type="number" id="set-passing_grade" class="form-fld" min="0" max="100" placeholder="75">
                            </div>
                            <div class="col-md-3">
                                <label class="form-lbl">Grade Scale Maximum</label>
                                <input type="number" id="set-grade_scale_max" class="form-fld" min="0" max="100" placeholder="100">
                            </div>
                        </div>
                        <div class="stg-section-label">Grade Component Weights</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-lbl">Written Works (%)</label>
                                <input type="number" id="set-ww_weight" class="form-fld" min="0" max="100" placeholder="30">
                            </div>
                            <div class="col-md-4">
                                <label class="form-lbl">Performance Task (%)</label>
                                <input type="number" id="set-pt_weight" class="form-fld" min="0" max="100" placeholder="50">
                            </div>
                            <div class="col-md-4">
                                <label class="form-lbl">Assessment (%)</label>
                                <input type="number" id="set-qa_weight" class="form-fld" min="0" max="100" placeholder="20">
                            </div>
                            <div class="col-12">
                                <div id="weight-total" class="small text-muted">Total: 100%</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button class="btn-dash btn-primary" onclick="saveSettings('academic')">
                                <i class="bi bi-floppy-fill me-1"></i> Save Academic Settings
                            </button>
                        </div>
                    </div>
                </div>

                
                <div class="content-card settings-tab" id="tab-security" style="display:none;">
                    <div class="content-card-header" style="border-left:4px solid var(--red);padding-left:16px;">
                        <div>
                            <h6 style="margin:0;"><i class="bi bi-shield-lock me-2" style="color:var(--red);"></i>Security &amp; System Settings</h6>
                            <div style="font-size:11px;color:var(--muted);margin-top:2px;">Configure authentication and session policies</div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="stg-section-label">Session &amp; Login</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-lbl">Session Timeout (min)</label>
                                <input type="number" id="set-session_timeout" class="form-fld" min="5" placeholder="120">
                            </div>
                            <div class="col-md-3">
                                <label class="form-lbl">Max Login Attempts</label>
                                <input type="number" id="set-max_login_attempts" class="form-fld" min="1" placeholder="5">
                            </div>
                            <div class="col-md-3">
                                <label class="form-lbl">Lockout Duration (min)</label>
                                <input type="number" id="set-lockout_duration" class="form-fld" min="1" placeholder="15">
                            </div>
                        </div>
                        <div class="stg-section-label">Password Policy</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-lbl">Min Password Length</label>
                                <input type="number" id="set-password_min_length" class="form-fld" min="6" placeholder="8">
                            </div>
                            <div class="col-md-3">
                                <label class="form-lbl">Require Uppercase</label>
                                <select id="set-require_password_uppercase" class="form-fld">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-lbl">Require Number</label>
                                <select id="set-require_password_number" class="form-fld">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button class="btn-dash btn-primary" onclick="saveSettings('security')">
                                <i class="bi bi-floppy-fill me-1"></i> Save Security Settings
                            </button>
                        </div>
                    </div>
                </div>

                
                <div class="settings-tab" id="tab-account" style="display:none;">
                    <?php if(session('photo_success')): ?>
                        <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#2e7d32;display:flex;align-items:center;gap:8px;">
                            <i class="bi bi-check-circle-fill"></i> <?php echo e(session('photo_success')); ?>

                        </div>
                    <?php endif; ?>
                    <div class="row g-4">
                        
                        <div class="col-md-5">
                            <div class="content-card">
                                <div class="content-card-header" style="border-left:4px solid var(--blue);padding-left:16px;">
                                    <h6 style="margin:0;"><i class="bi bi-person-circle me-2" style="color:var(--blue);"></i>Account Information</h6>
                                </div>
                                <div class="p-4">
                                    <form method="POST" action="<?php echo e(route('admin.settings.photo')); ?>" enctype="multipart/form-data" id="admin-photo-form">
                                        <?php echo csrf_field(); ?>
                                        <div style="display:flex;flex-direction:column;align-items:center;gap:12px;margin-bottom:24px;padding:20px;background:linear-gradient(135deg,#f0f5ff,#fff);border-radius:12px;border:1px solid #e8f0fb;">
                                            <div style="position:relative;flex-shrink:0;width:88px;height:88px;">
                                                <?php if(Auth::user()->profile_photo): ?>
                                                    <img id="admin-avatar-img" src="<?php echo e(asset('storage/' . Auth::user()->profile_photo)); ?>" alt="Profile"
                                                         style="width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid var(--blue-pale);box-shadow:0 4px 12px rgba(26,58,108,.15);">
                                                <?php else: ?>
                                                    <div id="admin-avatar-placeholder" style="width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--blue-light));display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(26,58,108,.2);">
                                                        <span style="font-size:32px;font-weight:700;color:#fff;"><?php echo e(strtoupper(substr(Auth::user()->name,0,1))); ?></span>
                                                    </div>
                                                    <img id="admin-avatar-img" src="" alt="Profile"
                                                         style="width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid var(--blue-pale);display:none;">
                                                <?php endif; ?>
                                                <label for="admin-photo-input" title="Change photo"
                                                       style="position:absolute;bottom:2px;right:2px;width:26px;height:26px;border-radius:50%;background:var(--blue);border:2px solid #fff;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,.2);">
                                                    <i class="bi bi-camera-fill" style="font-size:12px;color:#fff;"></i>
                                                </label>
                                                <input type="file" id="admin-photo-input" name="photo" accept="image/jpeg,image/png,image/webp"
                                                       style="display:none;" onchange="previewAdminPhoto(this)">
                                            </div>
                                            <div style="text-align:center;">
                                                <div style="font-size:16px;font-weight:700;color:var(--text);"><?php echo e(Auth::user()->name); ?></div>
                                                <div style="font-size:12px;color:var(--muted);margin-top:2px;"><?php echo e(Auth::user()->email); ?></div>
                                                <span style="display:inline-block;background:#fef3e2;color:#b45309;border:1px solid #fcd34d;border-radius:20px;padding:2px 12px;font-size:11px;font-weight:600;margin-top:6px;">Admin / Registrar</span>
                                            </div>
                                        </div>
                                        <button type="submit" id="admin-photo-submit" style="display:none;"></button>
                                    </form>
                                    <div style="display:flex;flex-direction:column;gap:0;">
                                        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f5f5f5;">
                                            <span style="font-size:12px;color:var(--muted);font-weight:500;">Full Name</span>
                                            <span style="font-size:12px;font-weight:600;color:var(--text);"><?php echo e(Auth::user()->name); ?></span>
                                        </div>
                                        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f5f5f5;">
                                            <span style="font-size:12px;color:var(--muted);font-weight:500;">Email</span>
                                            <span style="font-size:12px;font-weight:600;color:var(--text);"><?php echo e(Auth::user()->email); ?></span>
                                        </div>
                                        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;">
                                            <span style="font-size:12px;color:var(--muted);font-weight:500;">Role</span>
                                            <span style="font-size:12px;font-weight:600;color:var(--text);">Admin / Registrar</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-md-7">
                            <div class="content-card">
                                <div class="content-card-header" style="border-left:4px solid var(--blue);padding-left:16px;">
                                    <h6 style="margin:0;"><i class="bi bi-lock-fill me-2" style="color:var(--blue);"></i>Change Password</h6>
                                </div>
                                <div class="p-4">
                                    <?php if(session('password_success')): ?>
                                        <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#2e7d32;display:flex;align-items:center;gap:8px;">
                                            <i class="bi bi-check-circle-fill"></i> <?php echo e(session('password_success')); ?>

                                        </div>
                                    <?php endif; ?>
                                    <?php if($errors->has('current_password')): ?>
                                        <div style="background:#fdecea;border:1px solid #f5c6cb;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#c0392b;display:flex;align-items:center;gap:8px;">
                                            <i class="bi bi-exclamation-circle-fill"></i> <?php echo e($errors->first('current_password')); ?>

                                        </div>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo e(route('admin.settings.password')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="mb-3">
                                            <label class="form-lbl">Current Password <span style="color:var(--red);">*</span></label>
                                            <input type="password" name="current_password" class="form-fld" placeholder="Enter your current password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-lbl">New Password <span style="color:var(--red);">*</span></label>
                                            <input type="password" name="password" class="form-fld" placeholder="At least 8 characters" required minlength="8">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-lbl">Confirm New Password <span style="color:var(--red);">*</span></label>
                                            <input type="password" name="password_confirmation" class="form-fld" placeholder="Re-enter new password" required>
                                        </div>
                                        <button type="submit" class="btn-dash btn-primary">
                                            <i class="bi bi-lock-fill me-1"></i> Update Password
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/settings.blade.php ENDPATH**/ ?>