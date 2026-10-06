        <div class="section-header">

            <div>

                <h1>Announcements</h1>

                <p>Post and manage school announcements.</p>

            </div>

        </div>

        
        <?php if(session('sa_success') && session('sa_section') === 'announcements'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:10px;margin-bottom:16px;">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('sa_success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        
        <div class="content-card mb-4">
            <div class="content-card-header"><h6><i class="bi bi-plus-circle me-2" style="color:var(--gold);"></i>Post New Announcement</h6></div>
            <div class="p-4">
                <form method="POST" action="<?php echo e(route('admin.announcements.store')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="audience" value="all">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-lbl">Title *</label>
                            <input type="text" name="title" class="form-fld" placeholder="Announcement title"
                                required autocapitalize="sentences"
                                oninput="this.value=this.value.charAt(0).toUpperCase()+this.value.slice(1)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-lbl">Category</label>
                            <select name="category" class="form-fld">
                                <option value="general">General</option>
                                <option value="academic">Academic</option>
                                <option value="enrollment">Enrollment</option>
                                <option value="activity">Activity / Event</option>
                                <option value="reminder">Reminder</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-lbl">Content *</label>
                            <textarea name="content" class="form-fld" rows="4" placeholder="Write your announcement here..." required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-lbl">Image (optional)</label>
                            <input type="file" name="image" class="form-fld" accept="image/jpg,image/jpeg,image/png,image/webp">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-dash btn-primary">
                                <i class="bi bi-send-fill"></i> Post Announcement
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="content-card">
            <div class="content-card-header">
                <h6><i class="bi bi-megaphone me-2" style="color:var(--gold);"></i>Posted Announcements</h6>
                <span style="font-size:12px;color:var(--muted);"><?php echo e(isset($announcements) ? $announcements->total() : 0); ?> total</span>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:6px;padding:12px 20px;border-bottom:1px solid #f0f0f0;">
                <?php $annCats = ['academic'=>'Academic','reminder'=>'Reminder','activity'=>'Activity','general'=>'General','enrollment'=>'Enrollment']; ?>
                <a href="<?php echo e(route('admin.section.announcements')); ?>" style="padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid <?php echo e(!($annCategoryFilter ?? null) ? '#1a3a6c' : '#e2e8f0'); ?>;background:<?php echo e(!($annCategoryFilter ?? null) ? '#1a3a6c' : '#fff'); ?>;color:<?php echo e(!($annCategoryFilter ?? null) ? '#fff' : '#64748b'); ?>;">All</a>
                <?php $__currentLoopData = $annCats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.section.announcements', ['ann_category' => $val])); ?>" style="padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid <?php echo e(($annCategoryFilter ?? null) === $val ? '#1a3a6c' : '#e2e8f0'); ?>;background:<?php echo e(($annCategoryFilter ?? null) === $val ? '#1a3a6c' : '#fff'); ?>;color:<?php echo e(($annCategoryFilter ?? null) === $val ? '#fff' : '#64748b'); ?>;"><?php echo e($label); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__empty_1 = true; $__currentLoopData = ($announcements ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div style="display:flex;align-items:flex-start;gap:14px;padding:14px 20px;border-bottom:1px solid #f0f0f0;">
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#e8f0fb;color:#1a3a6c;"><?php echo e(ucfirst($ann->category)); ?></span>
                        <?php if(!$ann->is_active): ?>
                        <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#f3f4f6;color:#6b7280;">Hidden</span>
                        <?php endif; ?>
                    </div>
                    <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:2px;"><?php echo e($ann->title); ?></div>
                    <div style="font-size:12px;color:var(--muted);"><?php echo e(Str::limit($ann->content, 100)); ?></div>
                    <div style="font-size:11px;color:var(--muted);margin-top:4px;"><i class="bi bi-clock me-1"></i><?php echo e($ann->created_at->format('M d, Y h:i A')); ?></div>
                </div>
                <div style="display:flex;gap:6px;flex-shrink:0;align-items:center;">
                    <button type="button" title="Edit"
                        onclick="openEditAnnouncementModal(<?php echo e($ann->id); ?>, <?php echo e(\Illuminate\Support\Js::from($ann->title)); ?>, <?php echo e(\Illuminate\Support\Js::from($ann->content)); ?>, <?php echo e(\Illuminate\Support\Js::from($ann->category)); ?>, <?php echo e(\Illuminate\Support\Js::from($ann->audience)); ?>, <?php echo e(\Illuminate\Support\Js::from($ann->created_at->format('Y-m-d\TH:i'))); ?>)"
                        style="padding:6px 10px;border-radius:8px;border:1.5px solid #e2e8f0;background:#f0f4ff;color:#1a3a6c;cursor:pointer;font-size:13px;">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form method="POST" action="<?php echo e(route('admin.announcements.toggle', $ann)); ?>" style="margin:0;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" title="<?php echo e($ann->is_active ? 'Hide' : 'Show'); ?>"
                            style="padding:6px 10px;border-radius:8px;border:1.5px solid #e2e8f0;background:<?php echo e($ann->is_active ? '#fff3e0' : '#f0fdf4'); ?>;color:<?php echo e($ann->is_active ? '#e65100' : '#16a34a'); ?>;cursor:pointer;font-size:13px;">
                            <i class="bi bi-<?php echo e($ann->is_active ? 'eye-slash' : 'eye'); ?>"></i>
                        </button>
                    </form>
                    <form method="POST" action="<?php echo e(route('admin.announcements.destroy', $ann)); ?>" style="margin:0;"
                          onsubmit="return confirm('Delete this announcement? This cannot be undone.')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" title="Delete"
                            style="padding:6px 10px;border-radius:8px;border:1.5px solid #fecaca;background:#fff5f5;color:#dc2626;cursor:pointer;font-size:13px;">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="padding:40px;text-align:center;color:var(--muted);">
                <i class="bi bi-megaphone" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                <?php echo e(($annCategoryFilter ?? null) ? 'No announcements in this category.' : 'No announcements posted yet.'); ?>

            </div>
            <?php endif; ?>
            <?php if(isset($announcements) && $announcements->lastPage() > 1): ?>
            <div style="padding:14px 20px;border-top:1px solid #f0f0f0;">
                <?php echo e($announcements->onEachSide(1)->links()); ?>

            </div>
            <?php endif; ?>
        </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/announcements-content.blade.php ENDPATH**/ ?>