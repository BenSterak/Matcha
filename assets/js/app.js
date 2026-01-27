/**
 * Matcha - Main Application JavaScript
 * Common functionality and utilities
 */

// Global app namespace
const Matcha = {
    // API helper
    async api(endpoint, options = {}) {
        const defaults = {
            headers: {
                'Content-Type': 'application/json'
            }
        };

        try {
            const response = await fetch('/api/' + endpoint, { ...defaults, ...options });
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            return { success: false, error: 'Network error' };
        }
    },

    // Check for new matches / notifications
    async checkNotifications() {
        try {
            const data = await this.api('matches.php?action=pending_count');
            if (data.success && data.count > 0) {
                const notificationDot = document.getElementById('matchNotification');
                if (notificationDot) {
                    notificationDot.style.display = 'block';
                }
            }
        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    },

    // Format salary
    formatSalary(amount) {
        if (!amount) return 'לא צוין';
        return new Intl.NumberFormat('he-IL').format(amount) + ' ₪';
    },

    // Format date
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('he-IL');
    },

    // Show toast notification
    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--secondary);
            color: white;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-full);
            font-size: 0.875rem;
            z-index: 1000;
            animation: slideUp 0.3s ease;
        `;

        if (type === 'success') {
            toast.style.background = 'var(--success)';
        } else if (type === 'error') {
            toast.style.background = 'var(--error)';
        }

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    },

    // Initialize
    init() {
        // Check for notifications periodically
        this.checkNotifications();
        setInterval(() => this.checkNotifications(), 30000);

        // Initialize Feather Icons
        if (window.feather) {
            feather.replace();
        }

        console.log('Matcha App initialized');
    }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    Matcha.init();
});
