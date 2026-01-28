<?php
$pageTitle = 'צ\'אט';
require_once 'includes/header.php';
requireAuth();

$matchId = $_GET['match'] ?? null;
$match = null;
$job = null;

if ($matchId) {
    // Get match details
    try {
        $stmt = $pdo->prepare("
            SELECT m.*, j.title as jobTitle, j.company, j.image as jobImage
            FROM matches m
            JOIN jobs j ON m.jobId = j.id
            WHERE m.id = ? AND m.userId = ? AND m.status = 'matched'
        ");
        $stmt->execute([$matchId, $_SESSION['user_id']]);
        $match = $stmt->fetch();
    } catch (PDOException $e) {
        $match = null;
    }
}
?>

<?php if ($match): ?>
    <!-- Chat View for specific match -->
    <header class="header">
        <a href="chat.php" class="header-icon-btn">
            <i data-feather="arrow-right"></i>
        </a>
        <div style="display: flex; align-items: center; gap: var(--spacing-sm);">
            <img src="<?php echo htmlspecialchars($match['jobImage'] ?: 'https://via.placeholder.com/36'); ?>"
                alt="<?php echo htmlspecialchars($match['company']); ?>"
                style="width: 36px; height: 36px; border-radius: var(--radius-md); object-fit: cover;">
            <div>
                <h1 style="font-size: 1rem; font-weight: 600;">
                    <?php echo htmlspecialchars($match['jobTitle']); ?>
                </h1>
                <p style="font-size: 0.75rem; color: var(--text-muted);">
                    <?php echo htmlspecialchars($match['company']); ?>
                </p>
            </div>
        </div>
        <div style="width: 44px;"></div>
    </header>

    <main style="flex: 1; display: flex; flex-direction: column; height: calc(100vh - 60px);">
        <div class="chat-messages" id="chatMessages">
            <!-- System message -->
            <div style="text-align: center; padding: var(--spacing-xl); color: var(--text-light);">
                <div
                    style="background: var(--success-light); color: var(--success); padding: var(--spacing-md); border-radius: var(--radius-lg); display: inline-block; margin-bottom: var(--spacing-md);">
                    <i data-feather="check-circle" style="width: 24px; height: 24px;"></i>
                </div>
                <p style="font-size: 0.875rem;">
                    התאמה! אתם יכולים עכשיו להתחיל לשוחח על המשרה.
                </p>
            </div>

            <!-- Messages will be loaded by JavaScript -->
        </div>

        <div class="chat-input-container">
            <input type="text" class="chat-input" placeholder="כתבו הודעה..." id="messageInput"
                onkeydown="if(event.key === 'Enter') sendMessage()">
            <button class="chat-send-btn" onclick="sendMessage()">
                <i data-feather="send" style="width: 20px; height: 20px;"></i>
            </button>
        </div>
    </main>

    <script>
        const matchId = <?php echo $matchId; ?>;
        let lastMessageId = 0;
        let isLoading = false;

        // Load existing messages on page load
        document.addEventListener('DOMContentLoaded', loadMessages);

        // Poll for new messages every 3 seconds
        setInterval(pollNewMessages, 3000);

        async function loadMessages() {
            try {
                const response = await fetch(`/api/messages.php?action=get&matchId=${matchId}`);
                const data = await response.json();

                if (data.success && data.messages.length > 0) {
                    const container = document.getElementById('chatMessages');
                    // Keep the system message
                    const systemMsg = container.querySelector('div[style*="text-align: center"]');

                    // Clear existing messages except system message
                    container.innerHTML = '';
                    if (systemMsg) container.appendChild(systemMsg);

                    data.messages.forEach(msg => {
                        addMessageToUI(msg.content, msg.isMine);
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    });
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }

        async function pollNewMessages() {
            if (isLoading) return;

            try {
                const response = await fetch(`/api/messages.php?action=get&matchId=${matchId}&after=${lastMessageId}`);
                const data = await response.json();

                if (data.success && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        if (!msg.isMine) { // Only add messages from others
                            addMessageToUI(msg.content, false);
                        }
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    });
                }
            } catch (error) {
                console.error('Error polling messages:', error);
            }
        }

        async function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();

            if (!message || isLoading) return;

            isLoading = true;
            input.disabled = true;

            try {
                const response = await fetch('/api/messages.php?action=send', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        matchId: matchId,
                        content: message
                    })
                });

                const data = await response.json();

                if (data.success) {
                    addMessageToUI(message, true);
                    lastMessageId = Math.max(lastMessageId, data.message.id);
                    input.value = '';
                } else {
                    alert(data.error || 'שגיאה בשליחת ההודעה');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('שגיאה בשליחת ההודעה');
            }

            isLoading = false;
            input.disabled = false;
            input.focus();
        }

        function addMessageToUI(content, isMine) {
            const container = document.getElementById('chatMessages');
            const messageEl = document.createElement('div');
            messageEl.className = 'chat-message ' + (isMine ? 'sent' : 'received');
            messageEl.textContent = content;
            container.appendChild(messageEl);
            container.scrollTop = container.scrollHeight;
        }
    </script>

<?php else: ?>
    <!-- Chat List View -->
    <header class="header">
        <div style="width: 44px;"></div>
        <h1 style="font-size: 1.125rem; font-weight: 600;">צ'אט</h1>
        <div style="width: 44px;"></div>
    </header>

    <main class="profile-page">
        <div id="chatList">
            <!-- Loading state -->
            <div class="loading-container" id="loadingChats">
                <div class="loading-spinner"></div>
                <p>טוען שיחות...</p>
            </div>
        </div>
    </main>

    <?php include 'includes/nav.php'; ?>

    <script>
        async function loadChats() {
            const listEl = document.getElementById('chatList');

            try {
                const response = await fetch('/api/matches.php?action=user&status=matched');
                const data = await response.json();

                if (data.success && data.matches && data.matches.length > 0) {
                    listEl.innerHTML = '';

                    data.matches.forEach(match => {
                        const card = document.createElement('div');
                        card.className = 'match-card';
                        card.onclick = () => window.location.href = '/chat.php?match=' + match.id;
                        card.innerHTML = `
                    <img class="match-image" src="${match.image || 'https://via.placeholder.com/60'}" alt="${match.title}">
                    <div class="match-info">
                        <h3 class="match-title">${match.title}</h3>
                        <p class="match-subtitle">${match.company}</p>
                    </div>
                    <i data-feather="message-circle" style="color: var(--primary);"></i>
                `;
                        listEl.appendChild(card);
                    });

                    feather.replace();
                } else {
                    listEl.innerHTML = `
                <div class="empty-state">
                    <i data-feather="message-circle"></i>
                    <h2>אין שיחות עדיין</h2>
                    <p>כשתקבלו התאמה, תוכלו להתחיל לשוחח כאן!</p>
                    <a href="matches.php" class="btn btn-primary" style="margin-top: var(--spacing-lg);">
                        צפייה בהתאמות
                    </a>
                </div>
            `;
                    feather.replace();
                }
            } catch (error) {
                console.error('Error loading chats:', error);
                listEl.innerHTML = `
            <div class="empty-state">
                <i data-feather="alert-circle"></i>
                <h2>אירעה שגיאה</h2>
                <p>לא הצלחנו לטעון את השיחות</p>
            </div>
        `;
                feather.replace();
            }
        }

        document.addEventListener('DOMContentLoaded', loadChats);
    </script>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>