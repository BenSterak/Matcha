<?php
// Navigation Component - Responsive (Bottom Nav for Mobile, Sidebar for Desktop)
$user = getCurrentUser();
$isEmployer = $user && $user['role'] === 'employer';

// Define menu items based on role
$menuItems = [];
if ($isEmployer) {
    $menuItems = [
        ['href' => '/business/dashboard.php', 'icon' => 'home', 'label' => 'ראשי', 'id' => 'dashboard'],
        ['href' => '/business/jobs.php', 'icon' => 'briefcase', 'label' => 'משרות', 'id' => 'jobs'],
        ['href' => '/business/candidates.php', 'icon' => 'users', 'label' => 'מועמדים', 'id' => 'candidates'],
        ['href' => '/chat.php', 'icon' => 'message-circle', 'label' => 'צ\'אט', 'id' => 'chat'],
        ['href' => '/profile.php', 'icon' => 'user', 'label' => 'פרופיל', 'id' => 'profile'],
    ];
} else {
    $menuItems = [
        ['href' => '/feed.php', 'icon' => 'home', 'label' => 'ראשי', 'id' => 'feed'],
        ['href' => '/matches.php', 'icon' => 'heart', 'label' => 'התאמות', 'id' => 'matches'],
        ['href' => '/chat.php', 'icon' => 'message-circle', 'label' => 'צ\'אט', 'id' => 'chat'],
        ['href' => '/profile.php', 'icon' => 'user', 'label' => 'פרופיל', 'id' => 'profile'],
    ];
}
?>

<!-- Mobile Bottom Navigation -->
<nav class="bottom-nav">
    <?php foreach ($menuItems as $item): ?>
        <a href="<?php echo $item['href']; ?>" class="nav-item <?php echo $currentPage === $item['id'] ? 'active' : ''; ?>">
            <i data-feather="<?php echo $item['icon']; ?>"></i>
            <span><?php echo $item['label']; ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<!-- Desktop Sidebar (Hidden on Mobile) -->
<aside class="desktop-sidebar d-none d-lg-flex">
    <div class="sidebar-header">
        <div class="logo-container">
            <img src="/assets/images/logo.png" alt="Matcha" class="sidebar-logo">
            <h1 class="sidebar-title">Matcha</h1>
        </div>
    </div>

    <div class="sidebar-menu">
        <?php foreach ($menuItems as $item): ?>
            <a href="<?php echo $item['href']; ?>"
                class="sidebar-item <?php echo $currentPage === $item['id'] ? 'active' : ''; ?>">
                <i data-feather="<?php echo $item['icon']; ?>"></i>
                <span><?php echo $item['label']; ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="sidebar-footer">
        <a href="/logout.php" class="sidebar-item logout">
            <i data-feather="log-out"></i>
            <span>התנתק</span>
        </a>
    </div>
</aside>

<style>
    /* Sidebar specific styles that might need PHP context or weren't fully in CSS */
    .d-none {
        display: none !important;
    }

    @media (min-width: 1024px) {
        .d-lg-flex {
            display: flex !important;
        }
    }

    .sidebar-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }

    .logo-container {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .sidebar-logo {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        object-fit: cover;
    }

    .sidebar-title {
        font-size: 1.5rem;
        color: var(--primary);
        margin: 0;
    }

    .sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex: 1;
    }

    .sidebar-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem 1rem;
        color: var(--text-muted);
        border-radius: var(--radius-md);
        transition: all 0.2s;
        font-weight: 500;
    }

    .sidebar-item:hover {
        background-color: var(--surface-hover);
        color: var(--primary);
        transform: translateX(-4px);
        /* Hebrew direction */
    }

    .sidebar-item.active {
        background-color: var(--primary-light);
        color: var(--primary);
    }

    .sidebar-item.logout {
        color: var(--error);
    }

    .sidebar-item.logout:hover {
        background-color: var(--error-bg);
        color: var(--error);
    }
</style>