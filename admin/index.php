<?php
$pageTitle = 'לוח בקרה - ניהול';
require_once '../includes/header.php';
requireAuth();

// Check admin permission
$user = getCurrentUser();
if (!$user || !$user['is_admin']) {
    header('Location: /feed.php');
    exit;
}
?>

<!-- Header -->
<header class="header">
    <div style="width: 44px;"></div>
    <div style="display: flex; align-items: center; gap: 8px;">
        <img src="/assets/images/ICON.jpeg" alt="Matcha" class="header-logo">
        <span style="font-size: 0.75rem; background: var(--error); color: white; padding: 2px 8px; border-radius: var(--radius-full); font-weight: 600;">ADMIN</span>
    </div>
    <a href="/profile.php" class="header-icon-btn">
        <i data-feather="user"></i>
    </a>
</header>

<main class="main-content" style="padding: var(--spacing-lg); padding-bottom: 100px;">
    <h1 style="font-size: 1.5rem; margin-bottom: var(--spacing-lg);">לוח בקרה - ניהול</h1>

    <!-- Stats Grid -->
    <div class="admin-stats-grid" id="statsGrid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background: var(--primary-light); color: var(--primary);">
                <i data-feather="users"></i>
            </div>
            <div class="admin-stat-number" id="statTotalUsers">-</div>
            <div class="admin-stat-label">סה"כ משתמשים</div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background: #dbeafe; color: #2563eb;">
                <i data-feather="user-plus"></i>
            </div>
            <div class="admin-stat-number" id="statNewToday">-</div>
            <div class="admin-stat-label">חדשים היום</div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background: #fef3c7; color: #d97706;">
                <i data-feather="briefcase"></i>
            </div>
            <div class="admin-stat-number" id="statTotalJobs">-</div>
            <div class="admin-stat-label">משרות</div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background: #fce7f3; color: #db2777;">
                <i data-feather="heart"></i>
            </div>
            <div class="admin-stat-number" id="statTotalMatches">-</div>
            <div class="admin-stat-label">התאמות</div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background: #d1fae5; color: #059669;">
                <i data-feather="wifi"></i>
            </div>
            <div class="admin-stat-number" id="statOnlineNow">-</div>
            <div class="admin-stat-label">מחוברים עכשיו</div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background: #fee2e2; color: #dc2626;">
                <i data-feather="slash"></i>
            </div>
            <div class="admin-stat-number" id="statBlocked">-</div>
            <div class="admin-stat-label">חסומים</div>
        </div>
    </div>

    <!-- Role Breakdown -->
    <div class="card" style="padding: var(--spacing-lg); margin-bottom: var(--spacing-lg);">
        <h3 style="margin-bottom: var(--spacing-md);">
            <i data-feather="pie-chart" style="width: 18px; height: 18px;"></i>
            חלוקה לפי סוג
        </h3>
        <div style="display: flex; gap: var(--spacing-lg);">
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 0.875rem; color: var(--text-muted);">מחפשי עבודה</span>
                    <span style="font-size: 0.875rem; font-weight: 600;" id="statJobseekers">-</span>
                </div>
                <div style="height: 8px; background: var(--border); border-radius: 4px; overflow: hidden;">
                    <div id="barJobseekers" style="height: 100%; background: var(--primary); border-radius: 4px; width: 0%; transition: width 0.5s ease;"></div>
                </div>
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 0.875rem; color: var(--text-muted);">מעסיקים</span>
                    <span style="font-size: 0.875rem; font-weight: 600;" id="statEmployers">-</span>
                </div>
                <div style="height: 8px; background: var(--border); border-radius: 4px; overflow: hidden;">
                    <div id="barEmployers" style="height: 100%; background: #2563eb; border-radius: 4px; width: 0%; transition: width 0.5s ease;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management -->
    <div class="card" style="padding: var(--spacing-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg); flex-wrap: wrap; gap: var(--spacing-sm);">
            <h3 style="margin: 0;">
                <i data-feather="users" style="width: 18px; height: 18px;"></i>
                ניהול משתמשים
            </h3>
        </div>

        <!-- Search & Filter -->
        <div style="display: flex; gap: var(--spacing-sm); margin-bottom: var(--spacing-lg); flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px; position: relative;">
                <i data-feather="search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted);"></i>
                <input type="text" id="searchInput" placeholder="חיפוש לפי שם או אימייל..."
                    style="width: 100%; padding: 10px 40px 10px 12px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 0.875rem; background: var(--background);">
            </div>
            <select id="roleFilter" style="padding: 10px 12px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 0.875rem; background: var(--background); min-width: 120px;">
                <option value="">כל הסוגים</option>
                <option value="jobseeker">מחפשי עבודה</option>
                <option value="employer">מעסיקים</option>
                <option value="blocked">חסומים</option>
            </select>
        </div>

        <!-- Users Table -->
        <div style="overflow-x: auto;">
            <table class="admin-table" id="usersTable">
                <thead>
                    <tr>
                        <th>משתמש</th>
                        <th>אימייל</th>
                        <th>סוג</th>
                        <th>סטטוס</th>
                        <th>הצטרף</th>
                        <th>פעולות</th>
                    </tr>
                </thead>
                <tbody id="usersBody">
                    <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">טוען...</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pagination" style="display: flex; justify-content: center; gap: var(--spacing-sm); margin-top: var(--spacing-lg);"></div>
    </div>
</main>

<style>
    .admin-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
    }

    .admin-stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        text-align: center;
    }

    .admin-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto var(--spacing-sm);
    }

    .admin-stat-icon svg {
        width: 20px;
        height: 20px;
    }

    .admin-stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1;
        margin-bottom: 4px;
    }

    .admin-stat-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .admin-table th {
        text-align: right;
        padding: 12px 8px;
        border-bottom: 2px solid var(--border);
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.75rem;
        white-space: nowrap;
    }

    .admin-table td {
        padding: 12px 8px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .admin-table tr:hover {
        background: var(--background);
    }

    .admin-user-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .admin-user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        background: var(--border);
    }

    .admin-user-name {
        font-weight: 500;
        white-space: nowrap;
    }

    .admin-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: var(--radius-full);
        font-size: 0.7rem;
        font-weight: 600;
    }

    .admin-badge-jobseeker {
        background: var(--primary-light);
        color: var(--primary);
    }

    .admin-badge-employer {
        background: #dbeafe;
        color: #2563eb;
    }

    .admin-badge-blocked {
        background: #fee2e2;
        color: #dc2626;
    }

    .admin-badge-online {
        background: #d1fae5;
        color: #059669;
    }

    .admin-badge-offline {
        background: var(--background);
        color: var(--text-muted);
    }

    .admin-btn-block {
        padding: 6px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 0.75rem;
        font-weight: 500;
        cursor: pointer;
        background: var(--surface);
        color: var(--text-main);
        transition: all 0.2s;
    }

    .admin-btn-block:hover {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #dc2626;
    }

    .admin-btn-block.is-blocked {
        background: #d1fae5;
        border-color: #6ee7b7;
        color: #059669;
    }

    .admin-btn-block.is-blocked:hover {
        background: #a7f3d0;
    }

    .admin-page-btn {
        width: 36px;
        height: 36px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        background: var(--surface);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        color: var(--text-main);
        transition: all 0.2s;
    }

    .admin-page-btn:hover {
        background: var(--primary-light);
        border-color: var(--primary);
        color: var(--primary);
    }

    .admin-page-btn.active {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    @media (max-width: 600px) {
        .admin-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .admin-table th:nth-child(2),
        .admin-table td:nth-child(2),
        .admin-table th:nth-child(5),
        .admin-table td:nth-child(5) {
            display: none;
        }
    }
</style>

<?php include '../includes/nav.php'; ?>

<script>
    let currentPage = 1;
    let searchTimeout;

    // Load stats
    async function loadStats() {
        try {
            const res = await fetch('/api/admin.php?action=stats');
            const data = await res.json();
            if (data.success) {
                const s = data.stats;
                document.getElementById('statTotalUsers').textContent = s.totalUsers;
                document.getElementById('statNewToday').textContent = s.newToday;
                document.getElementById('statTotalJobs').textContent = s.totalJobs;
                document.getElementById('statTotalMatches').textContent = s.totalMatches;
                document.getElementById('statOnlineNow').textContent = s.onlineNow;
                document.getElementById('statBlocked').textContent = s.blockedUsers;
                document.getElementById('statJobseekers').textContent = s.jobseekers;
                document.getElementById('statEmployers').textContent = s.employers;

                // Role bars
                const total = s.totalUsers || 1;
                document.getElementById('barJobseekers').style.width = ((s.jobseekers / total) * 100) + '%';
                document.getElementById('barEmployers').style.width = ((s.employers / total) * 100) + '%';
            }
        } catch (e) {
            console.error('Error loading stats:', e);
        }
    }

    // Load users
    async function loadUsers(page = 1) {
        currentPage = page;
        const search = document.getElementById('searchInput').value;
        const role = document.getElementById('roleFilter').value;
        const params = new URLSearchParams({ action: 'users', page, search, role });

        try {
            const res = await fetch('/api/admin.php?' + params);
            const data = await res.json();
            if (data.success) {
                renderUsers(data.users);
                renderPagination(data.page, data.pages);
            }
        } catch (e) {
            console.error('Error loading users:', e);
        }
    }

    function renderUsers(users) {
        const tbody = document.getElementById('usersBody');
        if (users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">לא נמצאו משתמשים</td></tr>';
            return;
        }

        tbody.innerHTML = users.map(u => {
            const name = Matcha.escapeHtml(u.name || 'ללא שם');
            const email = Matcha.escapeHtml(u.email);
            const photo = u.photo
                ? Matcha.escapeHtml(u.photo)
                : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(u.name || '?') + '&background=random';
            const roleBadge = u.role === 'employer'
                ? '<span class="admin-badge admin-badge-employer">מעסיק</span>'
                : '<span class="admin-badge admin-badge-jobseeker">מחפש עבודה</span>';
            const isOnline = u.last_seen && (Date.now() - new Date(u.last_seen).getTime()) < 300000;
            const statusBadge = u.is_blocked
                ? '<span class="admin-badge admin-badge-blocked">חסום</span>'
                : isOnline
                    ? '<span class="admin-badge admin-badge-online">מחובר</span>'
                    : '<span class="admin-badge admin-badge-offline">לא מחובר</span>';
            const date = new Date(u.createdAt).toLocaleDateString('he-IL');
            const blockBtn = u.is_admin
                ? '<span style="font-size: 0.7rem; color: var(--text-muted);">מנהל</span>'
                : `<button class="admin-btn-block ${u.is_blocked ? 'is-blocked' : ''}" onclick="toggleBlock(${u.id}, this)">
                    ${u.is_blocked ? 'בטל חסימה' : 'חסום'}
                   </button>`;

            return `<tr>
                <td>
                    <div class="admin-user-cell">
                        <img src="${photo}" class="admin-user-avatar" alt="">
                        <span class="admin-user-name">${name}</span>
                    </div>
                </td>
                <td style="color: var(--text-muted); font-size: 0.8rem;">${email}</td>
                <td>${roleBadge}</td>
                <td>${statusBadge}</td>
                <td style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap;">${date}</td>
                <td>${blockBtn}</td>
            </tr>`;
        }).join('');
    }

    function renderPagination(current, total) {
        const div = document.getElementById('pagination');
        if (total <= 1) {
            div.innerHTML = '';
            return;
        }
        let html = '';
        for (let i = 1; i <= total; i++) {
            html += `<button class="admin-page-btn ${i === current ? 'active' : ''}" onclick="loadUsers(${i})">${i}</button>`;
        }
        div.innerHTML = html;
    }

    // Toggle block
    async function toggleBlock(userId, btn) {
        const isBlocked = btn.classList.contains('is-blocked');
        const action = isBlocked ? 'ביטול חסימה' : 'חסימה';
        if (!confirm(`האם לבצע ${action} למשתמש זה?`)) return;

        try {
            const res = await fetch('/api/admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=toggle_block&user_id=${userId}`
            });
            const data = await res.json();
            if (data.success) {
                Matcha.showToast(data.message, 'success');
                loadUsers(currentPage);
                loadStats();
            } else {
                Matcha.showToast(data.error, 'error');
            }
        } catch (e) {
            Matcha.showToast('שגיאה בביצוע הפעולה', 'error');
        }
    }

    // Search debounce
    document.getElementById('searchInput').addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadUsers(1), 300);
    });

    document.getElementById('roleFilter').addEventListener('change', () => loadUsers(1));

    // Initial load
    loadStats();
    loadUsers();
</script>

<?php include '../includes/footer.php'; ?>
