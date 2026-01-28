/**
 * Matcha - Swipe Deck Component
 * Vanilla JavaScript implementation of Tinder-like swipe cards
 */

class SwipeDeck {
    constructor(options = {}) {
        this.wrapper = document.getElementById('deckWrapper');
        this.loadingState = document.getElementById('loadingState');
        this.cardTemplate = document.getElementById('jobCardTemplate');
        this.emptyTemplate = document.getElementById('emptyStateTemplate');

        this.jobs = [];
        this.currentIndex = 0;
        this.cards = [];

        // Touch/Mouse tracking
        this.isDragging = false;
        this.startX = 0;
        this.startY = 0;
        this.currentX = 0;
        this.currentCard = null;

        // Swipe threshold
        this.threshold = 100;

        this.init();
    }

    async init() {
        await this.loadJobs();
        this.renderCards();
        this.bindEvents();
    }

    async loadJobs() {
        try {
            const response = await fetch('/api/jobs.php?action=feed');
            const data = await response.json();

            if (data.success && data.jobs) {
                this.jobs = data.jobs;
            } else {
                // Use dummy data if no jobs
                this.jobs = this.getDummyJobs();
            }
        } catch (error) {
            console.error('Error loading jobs:', error);
            // Use dummy data on error
            this.jobs = this.getDummyJobs();
        }

        this.loadingState.style.display = 'none';
    }

    getDummyJobs() {
        return [
            {
                id: 1,
                title: 'מפתח/ת Full Stack',
                company: 'TechCo',
                description: 'אנחנו מחפשים מפתח/ת Full Stack עם ניסיון ב-React ו-Node.js. תהיו חלק מצוות מדהים שמפתח מוצרים חדשניים.',
                location: 'תל אביב',
                salaryRange: '25,000-35,000 ₪',
                type: 'משרה מלאה',
                image: 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=500'
            },
            {
                id: 2,
                title: 'מעצב/ת UX/UI',
                company: 'DesignHub',
                description: 'מעצב/ת UX/UI עם עין לפרטים ויכולת לעבוד בסביבה דינמית. ניסיון עם Figma חובה.',
                location: 'הרצליה',
                salaryRange: '20,000-28,000 ₪',
                type: 'היברידי',
                image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=500'
            },
            {
                id: 3,
                title: 'מנהל/ת שיווק דיגיטלי',
                company: 'MarketPro',
                description: 'מנהל/ת שיווק דיגיטלי עם ניסיון בקמפיינים ברשתות חברתיות ו-Google Ads.',
                location: 'רמת גן',
                salaryRange: '18,000-25,000 ₪',
                type: 'מהבית',
                image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=500'
            },
            {
                id: 4,
                title: 'Data Analyst',
                company: 'DataCo',
                description: 'אנליסט/ית נתונים עם ידע ב-SQL ו-Python. הזדמנות להשפיע על החלטות עסקיות משמעותיות.',
                location: 'תל אביב',
                salaryRange: '22,000-30,000 ₪',
                type: 'משרה מלאה',
                image: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=500'
            }
        ];
    }

    renderCards() {
        // Clear existing cards (except loading)
        document.querySelectorAll('.swipe-card, .empty-state').forEach(el => el.remove());

        if (this.currentIndex >= this.jobs.length) {
            this.showEmptyState();
            return;
        }

        // Render current and next card
        for (let i = Math.min(this.currentIndex + 1, this.jobs.length - 1); i >= this.currentIndex; i--) {
            this.renderCard(this.jobs[i], i === this.currentIndex);
        }

        // Reinitialize feather icons
        if (window.feather) {
            feather.replace();
        }
    }

    renderCard(job, isTop = false) {
        const template = this.cardTemplate.content.cloneNode(true);
        const card = template.querySelector('.swipe-card');

        card.dataset.jobId = job.id;
        // Auto-generate image from company name if none provided
        const companyImage = job.image || `https://ui-avatars.com/api/?name=${encodeURIComponent(job.company || job.title)}&size=400&background=22C55E&color=fff&font-size=0.4&bold=true`;
        card.querySelector('.job-card-image').src = companyImage;
        card.querySelector('.job-card-image').alt = job.title;
        card.querySelector('.job-card-title').textContent = job.title;
        card.querySelector('.job-card-company').textContent = job.company;
        card.querySelector('.salary-tag span').textContent = job.salaryRange || job.salary || 'לא צוין';
        card.querySelector('.location-tag span').textContent = job.location || 'לא צוין';
        card.querySelector('.type-tag span').textContent = job.type || 'משרה מלאה';
        card.querySelector('.job-description p').textContent = job.description;

        if (!isTop) {
            card.classList.add('card-behind');
            card.style.transform = 'scale(0.95) translateY(10px)';
            card.style.zIndex = '5';
        }

        this.wrapper.appendChild(card);

        if (isTop) {
            this.currentCard = card;
            this.bindCardEvents(card);
        }
    }

    showEmptyState() {
        const template = this.emptyTemplate.content.cloneNode(true);
        this.wrapper.appendChild(template);
        if (window.feather) {
            feather.replace();
        }
    }

    bindEvents() {
        // Button clicks are handled by global functions
    }

    bindCardEvents(card) {
        // Touch events
        card.addEventListener('touchstart', (e) => this.handleDragStart(e), { passive: true });
        card.addEventListener('touchmove', (e) => this.handleDragMove(e), { passive: false });
        card.addEventListener('touchend', (e) => this.handleDragEnd(e));

        // Mouse events
        card.addEventListener('mousedown', (e) => this.handleDragStart(e));
        document.addEventListener('mousemove', (e) => this.handleDragMove(e));
        document.addEventListener('mouseup', (e) => this.handleDragEnd(e));
    }

    handleDragStart(e) {
        if (!this.currentCard) return;

        this.isDragging = true;
        this.currentCard.style.transition = 'none';

        if (e.type === 'touchstart') {
            this.startX = e.touches[0].clientX;
            this.startY = e.touches[0].clientY;
        } else {
            this.startX = e.clientX;
            this.startY = e.clientY;
        }
    }

    handleDragMove(e) {
        if (!this.isDragging || !this.currentCard) return;

        let clientX, clientY;
        if (e.type === 'touchmove') {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
            e.preventDefault();
        } else {
            clientX = e.clientX;
            clientY = e.clientY;
        }

        this.currentX = clientX - this.startX;
        const rotation = this.currentX * 0.1;

        this.currentCard.style.transform = `translateX(${this.currentX}px) rotate(${rotation}deg)`;

        // Update swipe indicators
        const likeIndicator = this.currentCard.querySelector('.swipe-indicator.like');
        const nopeIndicator = this.currentCard.querySelector('.swipe-indicator.nope');

        if (this.currentX > 20) {
            likeIndicator.style.opacity = Math.min(this.currentX / this.threshold, 1);
            nopeIndicator.style.opacity = 0;
        } else if (this.currentX < -20) {
            nopeIndicator.style.opacity = Math.min(Math.abs(this.currentX) / this.threshold, 1);
            likeIndicator.style.opacity = 0;
        } else {
            likeIndicator.style.opacity = 0;
            nopeIndicator.style.opacity = 0;
        }
    }

    handleDragEnd(e) {
        if (!this.isDragging || !this.currentCard) return;

        this.isDragging = false;
        this.currentCard.style.transition = 'transform 0.3s ease';

        if (Math.abs(this.currentX) > this.threshold) {
            // Swipe detected
            const direction = this.currentX > 0 ? 'right' : 'left';
            this.swipe(direction);
        } else {
            // Return to center
            this.currentCard.style.transform = 'translateX(0) rotate(0deg)';
            this.currentCard.querySelector('.swipe-indicator.like').style.opacity = 0;
            this.currentCard.querySelector('.swipe-indicator.nope').style.opacity = 0;
        }

        this.currentX = 0;
    }

    swipe(direction) {
        if (!this.currentCard || this.currentIndex >= this.jobs.length) return;

        const job = this.jobs[this.currentIndex];
        const card = this.currentCard;

        // Animate out
        const exitX = direction === 'right' ? window.innerWidth : -window.innerWidth;
        const rotation = direction === 'right' ? 20 : -20;

        card.style.transition = 'transform 0.4s ease';
        card.style.transform = `translateX(${exitX}px) rotate(${rotation}deg)`;

        // Record swipe
        this.recordSwipe(job.id, direction);

        // Move to next card
        setTimeout(() => {
            card.remove();
            this.currentIndex++;
            this.currentCard = null;

            // Promote next card
            const nextCard = this.wrapper.querySelector('.swipe-card.card-behind');
            if (nextCard) {
                nextCard.classList.remove('card-behind');
                nextCard.style.transition = 'transform 0.3s ease';
                nextCard.style.transform = '';
                nextCard.style.zIndex = '10';
                this.currentCard = nextCard;
                this.bindCardEvents(nextCard);

                // Add another card behind if available
                if (this.currentIndex + 1 < this.jobs.length) {
                    this.renderCard(this.jobs[this.currentIndex + 1], false);
                }
            } else if (this.currentIndex >= this.jobs.length) {
                this.showEmptyState();
            }
        }, 300);
    }

    async recordSwipe(jobId, direction) {
        try {
            await fetch('/api/matches.php?action=swipe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    jobId: jobId,
                    action: direction === 'right' ? 'like' : 'pass'
                })
            });
        } catch (error) {
            console.error('Error recording swipe:', error);
        }

        console.log(`Swiped ${direction} on job ${jobId}`);
    }
}

// Global instance
let deck;

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    deck = new SwipeDeck();
});

// Global function for button clicks
function swipeCard(direction) {
    if (deck) {
        deck.swipe(direction);
    }
}
