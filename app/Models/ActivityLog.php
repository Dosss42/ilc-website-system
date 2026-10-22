<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    /** Every event_type the system records => label. Shared by the Admin Audit Trail and
     * the Super Admin System Logs filters, so both list the same events. */
    public const EVENT_TYPES = [
        'login' => 'Login', 'logout' => 'Logout', 'failed_login' => 'Failed Login', 'denied' => 'Denied Attempt',
        'password_change' => 'Password Change',
        'create' => 'Create', 'update' => 'Update', 'delete' => 'Delete',
        'cash_payment' => 'Cash Payment', 'walkin_payment' => 'Walk-in Payment',
        'payment_processed' => 'Payment Processed', 'payment_approved' => 'Payment Approved', 'payment_rejected' => 'Payment Rejected',
        'xendit_link_generated' => 'Online Payment Started', 'xendit_payment_completed' => 'Online Payment Completed',
        'error' => 'Error',
    ];

    public const ROLES = [
        'superadmin' => 'Super Admin', 'admin' => 'Admin', 'finance' => 'Finance',
        'cashier' => 'Cashier', 'teacher' => 'Teacher', 'student' => 'Student',
    ];

    public static function eventLabel(?string $type): string
    {
        return self::EVENT_TYPES[$type] ?? ucwords(str_replace('_', ' ', (string) $type));
    }

    protected $fillable = [
        'user_id', 'user_name', 'user_role',
        'event_type', 'description',
        'subject_type', 'subject_id',
        'ip_address', 'extra',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
