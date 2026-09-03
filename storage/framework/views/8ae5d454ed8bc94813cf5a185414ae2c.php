<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 280px; min-height: 100vh;">
    <a href="<?php echo e(route('student.portal')); ?>" class="d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none border-bottom">
        <i class="bi bi-mortarboard-fill me-2"></i>
        <span class="fs-5 fw-semibold">Student Portal</span>
    </a>
    
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="<?php echo e(route('student.portal')); ?>" 
               class="nav-link <?php echo e(request()->routeIs('student.portal') ? 'active' : ''); ?> d-flex align-items-center">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('student.info')); ?>" 
               class="nav-link <?php echo e(request()->routeIs('student.info') ? 'active' : ''); ?> d-flex align-items-center">
                <i class="bi bi-person me-2"></i>
                My Information
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('student.documents')); ?>" 
               class="nav-link <?php echo e(request()->routeIs('student.documents') ? 'active' : ''); ?> d-flex align-items-center">
                <i class="bi bi-file-earmark me-2"></i>
                Documents
            </a>
        </li>
    </ul>
    
    <hr>
    
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" 
           id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="user-avatar me-2" style="width: 32px; height: 32px; border-radius: 50%; background: #1a3a6c; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

            </div>
            <strong><?php echo e(Auth::user()->name); ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="<?php echo e(route('student.info')); ?>">
                <i class="bi bi-person-gear me-2"></i>Profile Settings
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?php echo e(route('logout')); ?>" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right me-2"></i>Sign out
            </a></li>
        </ul>
    </div>
    
    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
    </form>
</div>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\partials\student-sidebar.blade.php ENDPATH**/ ?>