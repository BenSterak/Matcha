<?php
$pageTitle = 'ההתאמות שלי';
require_once 'includes/header.php';
requireAuth();

$user = getCurrentUser();
?>

<!-- Header -->
<header class="header">
    <a href="feed.php" class="header-icon-btn">
        <i data-feather="arrow-right"></i>
    </a>
    <h1 style="font-size: 1.125rem; font-weight: 600;">ההתאמות שלי</h1>
    <div style="width: 44px;"></div>
</header>

<!-- Tabs -->
<div class="matches-tabs">
    <div class="matches-tab active" data-status="matched" onclick="filterMatches('matched')">
        <i data-feather="heart" style="width: 16px; height: 16px; margin-left: 4px;"></i>
        התאמות
    </div>
    <div class="matches-tab" data-status="pending" onclick="filterMatches('pending')">
        <i data-feather="clock" style="width: 16px; height: 16px; margin-left: 4px;"></i>
        ממתינים
    </div>
</div>

<!-- Matches List -->
<main class="matches-page">
    <div class="matches-list" id="matchesList">
        <div class="loading-container">
            <div class="loading-spinner"></div>
            <p>טוען התאמות...</p>
        </div>
    </div>
</main>

<?php include 'includes/nav.php'; ?>

<!-- Match Card Template -->
<template id="matchCardTemplate">
    <div class="match-card" onclick="openChat(this.dataset.matchId)">
        <img class="match-image" src="" alt="">
        <div class="match-info">
            <h3 class="match-title"></h3>
            <p class="match-subtitle"></p>
        </div>
        <span class="match-status"></span>
    </div>
</template>

<!-- Empty State Template -->
<template id="emptyMatchesTemplate">
    <div class="empty-state">
        <i data-feather="heart"></i>
        <h2>אין התאמות עדיין</h2>
        <p>המשיכו לגלות משרות והחליקו ימינה על אלה שמעניינות אתכם!</p>
        <a href="feed.php" class="btn btn-primary" style="margin-top: var(--spacing-lg);">
            חזרה לפיד
        </a>
    </div>
</template>

<script>
    let currentStatus = 'matched';

    async function loadMatches(status = 'matched') {
        const listEl = document.getElementById('matchesList');
        listEl.innerHTML = `
        <div class="loading-container">
            <div class="loading-spinner"></div>
            <p>טוען התאמות...</p>
        </div>
    `;

        try {
            const response = await fetch(`/api/matches.php?action=user&status=${status}`);
            const data = await response.json();

            if (data.success && data.matches && data.matches.length > 0) {
                renderMatches(data.matches, status);
            } else {
                showEmptyState();
            }
        } catch (error) {
            console.error('Error loading matches:', error);
            showEmptyState();
        }
    }

    function renderMatches(matches, status) {
        const listEl = document.getElementById('matchesList');
        const template = document.getElementById('matchCardTemplate');

        listEl.innerHTML = '';

        matches.forEach(match => {
            const card = template.content.cloneNode(true).querySelector('.match-card');

            card.dataset.matchId = match.id;
            card.dataset.jobId = match.jobId;
            card.querySelector('.match-image').src = match.image || 'https://via.placeholder.com/120';
            card.querySelector('.match-image').alt = match.title;
            card.querySelector('.match-title').textContent = match.title;
            card.querySelector('.match-subtitle').textContent = match.company + ' • ' + match.location;

            const statusEl = card.querySelector('.match-status');
            if (status === 'matched') {
                statusEl.textContent = 'התאמה!';
                statusEl.classList.add('matched');
            } else {
                statusEl.textContent = 'ממתין';
                statusEl.classList.add('pending');
            }

            listEl.appendChild(card);
        });

        feather.replace();
    }

    function showEmptyState() {
        const listEl = document.getElementById('matchesList');
        const template = document.getElementById('emptyMatchesTemplate');

        listEl.innerHTML = '';
        listEl.appendChild(template.content.cloneNode(true));

        feather.replace();
    }

    function filterMatches(status) {
        currentStatus = status;

        // Update tabs
        document.querySelectorAll('.matches-tab').forEach(tab => {
            tab.classList.toggle('active', tab.dataset.status === status);
        });

        loadMatches(status);
    }

    function openChat(matchId) {
        window.location.href = `/chat.php?match=${matchId}`;
    }

    // Load initial matches
    document.addEventListener('DOMContentLoaded', () => {
        loadMatches('matched');
    });
</script>

<?php include 'includes/footer.php'; ?>