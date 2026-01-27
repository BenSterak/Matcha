<?php
// Bottom navigation component
$user = getCurrentUser();
$isEmployer = $user && $user['role'] === 'employer';
?>

<?php if ($isEmployer): ?>
    <!-- Employer Navigation -->
    <nav class="bottom-nav">
        <a href="/business/dashboard.php" class="nav-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
            <i data-feather="home"></i>
            <span>ראשי</span>
        </a>
        <a href="/business/jobs.php" class="nav-item <?php echo $currentPage === 'jobs' ? 'active' : ''; ?>">
            <i data-feather="briefcase"></i>
            <span>משרות</span>
        </a>
        <a href="/business/candidates.php" class="nav-item <?php echo $currentPage === 'candidates' ? 'active' : ''; ?>">
            <i data-feather="users"></i>
            <span>מועמדים</span>
        </a>
        <a href="/profile.php" class="nav-item <?php echo $currentPage === 'profile' ? 'active' : ''; ?>">
            <i data-feather="user"></i>
            <span>פרופיל</span>
        </a>
    </nav>
<?php else: ?>
    <!-- Job Seeker Navigation -->
    <nav class="bottom-nav">
        <a href="/feed.php" class="nav-item <?php echo $currentPage === 'feed' ? 'active' : ''; ?>">
            <i data-feather="home"></i>
            <span>ראשי</span>
        </a>
        <a href="/matches.php" class="nav-item <?php echo $currentPage === 'matches' ? 'active' : ''; ?>">
            <i data-feather="heart"></i>
            <span>התאמות</span>
        </a>
        <a href="/chat.php" class="nav-item <?php echo $currentPage === 'chat' ? 'active' : ''; ?>">
            <i data-feather="message-circle"></i>
            <span>צ'אט</span>
        </a>
        <a href="/profile.php" class="nav-item <?php echo $currentPage === 'profile' ? 'active' : ''; ?>">
            <i data-feather="user"></i>
            <span>פרופיל</span>
        </a>
    </nav>
<?php endif; ?>