<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <?php if (isBusiness()): ?>
        <a class="navbar-brand d-flex align-items-center gap-2" href="business_dashboard.php">
            <img src="assets/img/logo.svg" alt="Agora Campus" width="32" height="32" class="d-inline-block align-text-top">
            <span>Agora Campus</span>
        </a>
        <?php else: ?>
        <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
            <img src="assets/img/logo.svg" alt="Agora Campus" width="32" height="32" class="d-inline-block align-text-top">
            <span>Agora Campus</span>
        </a>
        <?php endif; ?>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <?php if (isBusiness()): ?>
                    <!-- Business Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="business_dashboard.php"><i class="fas fa-chart-pie me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="post_job.php"><i class="fas fa-plus-circle me-1"></i> Post Job</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="business_jobs.php"><i class="fas fa-briefcase me-1"></i> My Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="business_applications.php"><i class="fas fa-users me-1"></i> Applications</a>
                    </li>
                <?php else: ?>
                    <!-- Student Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jobs.php"><i class="fas fa-briefcase me-1"></i> Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="groups.php"><i class="fas fa-users me-1"></i> Groups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="marketplace.php"><i class="fas fa-store me-1"></i> Market</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="notifications.php">
                            <i class="fas fa-bell me-1"></i> Notifications
                            <?php if (isset($notifications_count) && $notifications_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; transform: translate(-50%, 5px) !important;">
                                <?php echo $notifications_count; ?>
                            </span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item border-start mx-2 d-none d-lg-block"></li>
                
                <li class="nav-item">
                    <a class="nav-link theme-toggle" href="#" id="themeToggle" title="Toggle Light/Dark Mode" aria-pressed="false">
                        <i class="fas fa-moon"></i>
                    </a>
                </li>
                
                <li class="nav-item dropdown ms-lg-2">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="fas fa-user small"></i>
                        </div>
                        <span class="d-none d-lg-inline"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                        <?php if (isBusiness()): ?>
                             <li><a class="dropdown-item" href="business_dashboard.php"><i class="fas fa-columns w-20px"></i> Dashboard</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle w-20px"></i> My Profile</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php" id="logoutBtn"><i class="fas fa-sign-out-alt w-20px"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
