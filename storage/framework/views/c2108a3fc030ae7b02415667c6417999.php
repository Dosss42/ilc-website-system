<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .alert { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 15px 0; color: #721c24; }
        .details { background: white; padding: 15px; margin: 15px 0; border: 1px solid #ddd; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .amount { font-size: 24px; font-weight: bold; color: #dc3545; }
        .late-fee { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Late Fee Applied</h2>
        </div>
        
        <div class="content">
            <p>Dear Parent/Guardian of <?php echo e($student->name); ?>,</p>
            
            <p>Your tuition payment for <strong><?php echo e($installment->month_name); ?></strong> remains unpaid after 2 weeks past the due date.</p>
            
            <div class="alert">
                <strong>⚠️ A late fee of ₱500 has been applied to your account.</strong>
            </div>
            
            <div class="details">
                <h3>Updated Payment Details:</h3>
                <p><strong>Student:</strong> <?php echo e($student->name); ?></p>
                <p><strong>Month:</strong> <?php echo e($installment->month_name); ?></p>
                <p><strong>Original Due Date:</strong> <?php echo e($dueDate); ?></p>
                <p><strong>Original Amount:</strong> ₱<?php echo e(number_format($installment->amount, 2)); ?></p>
                <p><strong>Late Fee:</strong> <span class="late-fee">+₱<?php echo e($lateFee); ?></span></p>
                <p><strong>New Total Due:</strong> <span class="amount">₱<?php echo e($totalDue); ?></span></p>
            </div>
            
            <div class="alert">
                <strong>⚠️ IMPORTANT:</strong> If payment is not received within the next week, your student portal access will be blocked until the balance is settled.
            </div>
            
            <p><strong>Payment Options:</strong></p>
            <ul>
                <li>GCash - Transfer to school account and upload screenshot</li>
                <li>Cash - Pay directly at the school cashier</li>
            </ul>
            
            <p><a href="<?php echo e(route('student.portal')); ?>" style="background: #dc3545; color: white; padding: 12px 24px; text-decoration: none; display: inline-block; border-radius: 4px;">Pay Now - Access Portal</a></p>
        </div>
        
        <div class="footer">
            <p>Please settle your balance immediately to avoid portal access restrictions.</p>
            <p><strong><?php echo e(config('app.name')); ?></strong><br>
            This is an automated notification. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\emails\late-fee-applied.blade.php ENDPATH**/ ?>