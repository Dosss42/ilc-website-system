    <div id="section-messages" class="dash-section" style="display:none;">
        <div class="section-header-bar">
            <h4><i class="bi bi-envelope-fill me-2" style="color:var(--ilc-gold);"></i>Contact Messages</h4>
            <small class="text-muted">Messages submitted through the website contact form</small>
        </div>

        
        <?php
            $msgUnread  = $unreadMessagesCount ?? 0;
            $msgRead    = $readMessagesCount ?? 0;
            $msgReplied = $repliedMessagesCount ?? 0;
            $msgTotal   = $totalMessagesCount ?? 0;
        ?>
        <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:22px;">
            <div style="flex:1;min-width:120px;background:#fff;border:1px solid #e8edf5;border-radius:12px;padding:16px 18px;text-align:center;">
                <div style="font-size:26px;font-weight:800;color:#1a3a6c;"><?php echo e($msgTotal); ?></div>
                <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-top:2px;">Total</div>
            </div>
            <div style="flex:1;min-width:120px;background:#fef3c7;border:1px solid #fcd34d;border-radius:12px;padding:16px 18px;text-align:center;">
                <div style="font-size:26px;font-weight:800;color:#92400e;"><?php echo e($msgUnread); ?></div>
                <div style="font-size:11px;color:#92400e;font-weight:600;text-transform:uppercase;margin-top:2px;">Unread</div>
            </div>
            <div style="flex:1;min-width:120px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px 18px;text-align:center;">
                <div style="font-size:26px;font-weight:800;color:#1d4ed8;"><?php echo e($msgRead); ?></div>
                <div style="font-size:11px;color:#1d4ed8;font-weight:600;text-transform:uppercase;margin-top:2px;">Read</div>
            </div>
            <div style="flex:1;min-width:120px;background:#f0fdf4;border:1px solid #86efac;border-radius:12px;padding:16px 18px;text-align:center;">
                <div style="font-size:26px;font-weight:800;color:#15803d;"><?php echo e($msgReplied); ?></div>
                <div style="font-size:11px;color:#15803d;font-weight:600;text-transform:uppercase;margin-top:2px;">Replied</div>
            </div>
        </div>

        
        <div style="background:#fff;border:1px solid #e8edf5;border-radius:14px;overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-weight:700;font-size:14px;color:#1a2a4a;">Inbox</span>
            </div>
            <?php if(($contactMessages ?? collect())->isEmpty()): ?>
                <div style="text-align:center;padding:60px 20px;color:#94a3b8;">
                    <i class="bi bi-inbox" style="font-size:48px;display:block;margin-bottom:12px;opacity:.4;"></i>
                    <div style="font-size:14px;">No messages yet.</div>
                </div>
            <?php else: ?>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:#f8faff;">
                            <th style="padding:11px 16px;text-align:left;font-weight:700;color:#374151;border-bottom:1px solid #e8edf5;width:140px;">Sender</th>
                            <th style="padding:11px 16px;text-align:left;font-weight:700;color:#374151;border-bottom:1px solid #e8edf5;">Subject</th>
                            <th style="padding:11px 16px;text-align:left;font-weight:700;color:#374151;border-bottom:1px solid #e8edf5;width:110px;">Date</th>
                            <th style="padding:11px 16px;text-align:left;font-weight:700;color:#374151;border-bottom:1px solid #e8edf5;width:90px;">Status</th>
                            <th style="padding:11px 16px;text-align:left;font-weight:700;color:#374151;border-bottom:1px solid #e8edf5;width:140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $contactMessages ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isUnread = $msg->status === 'unread';
                            $statusConfig = [
                                'unread'  => ['#fef3c7','#92400e','Unread'],
                                'read'    => ['#eff6ff','#1d4ed8','Read'],
                                'replied' => ['#f0fdf4','#15803d','Replied'],
                            ][$msg->status] ?? ['#f3f4f6','#6b7280','—'];
                        ?>
                        <tr id="msg-row-<?php echo e($msg->id); ?>"
                            style="border-bottom:1px solid #f0f4f8;background:<?php echo e($isUnread ? '#fffbeb' : '#fff'); ?>;cursor:pointer;"
                            onclick="openMsgModal(<?php echo e($msg->id); ?>, <?php echo e(json_encode($msg->name)); ?>, <?php echo e(json_encode($msg->email)); ?>, <?php echo e(json_encode($msg->phone ?? '—')); ?>, <?php echo e(json_encode($msg->subject)); ?>, <?php echo e(json_encode($msg->message)); ?>, <?php echo e(json_encode($msg->created_at->format('M d, Y h:i A'))); ?>, <?php echo e(json_encode($msg->status)); ?>)">
                            <td style="padding:12px 16px;">
                                <div style="font-weight:<?php echo e($isUnread ? '700' : '500'); ?>;color:#1e293b;"><?php echo e($msg->name); ?></div>
                                <div style="font-size:11px;color:#94a3b8;margin-top:1px;"><?php echo e($msg->email); ?></div>
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="font-weight:<?php echo e($isUnread ? '700' : '400'); ?>;color:#1e293b;"><?php echo e($msg->subject); ?></div>
                                <div style="font-size:11px;color:#94a3b8;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:300px;"><?php echo e(Str::limit($msg->message, 70)); ?></div>
                            </td>
                            <td style="padding:12px 16px;font-size:12px;color:#64748b;white-space:nowrap;"><?php echo e($msg->created_at->format('M d, Y')); ?><br><?php echo e($msg->created_at->format('h:i A')); ?></td>
                            <td style="padding:12px 16px;">
                                <span style="background:<?php echo e($statusConfig[0]); ?>;color:<?php echo e($statusConfig[1]); ?>;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;"><?php echo e($statusConfig[2]); ?></span>
                            </td>
                            <td style="padding:12px 16px;" onclick="event.stopPropagation();">
                                <div style="display:flex;gap:5px;flex-wrap:wrap;">
                                    <?php if($msg->status !== 'read'): ?>
                                    <button onclick="updateMsgStatus(<?php echo e($msg->id); ?>, 'read')" title="Mark as Read"
                                        style="padding:4px 9px;background:#eff6ff;color:#1d4ed8;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php endif; ?>
                                    <?php if($msg->status !== 'replied'): ?>
                                    <button onclick="updateMsgStatus(<?php echo e($msg->id); ?>, 'replied')" title="Mark as Replied"
                                        style="padding:4px 9px;background:#f0fdf4;color:#15803d;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                                        <i class="bi bi-reply-fill"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button onclick="deleteMsg(<?php echo e($msg->id); ?>)" title="Delete"
                                        style="padding:4px 9px;background:#fef2f2;color:#dc2626;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views/admin/sections/messages.blade.php ENDPATH**/ ?>