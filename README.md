# Matcha - Job Matching Platform

<p align="center">
  <img src="assets/images/LOGO.jpeg" alt="Matcha Logo" width="120">
</p>

<p align="center">
  <strong>מצא את העבודה הבאה שלך בהחלקה</strong><br>
  A Tinder-like job matching platform built with PHP, HTML, CSS, and JavaScript
</p>

---

## ✨ Features

### For Job Seekers
- 📱 **Swipe Interface** - Tinder-like swiping for job discovery
- 💚 **Match System** - Get notified when companies like you back
- 💬 **Chat** - Direct messaging with matched companies
- 👤 **Profile** - Showcase your skills and preferences

### For Employers
- 📋 **Job Posting** - Create and manage job listings
- 👥 **Candidate Review** - Swipe through interested candidates
- 📊 **Dashboard** - Track active jobs and pending matches
- ✅ **Quick Actions** - Approve or reject with one tap

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server (or use XAMPP)

### Installation

1. **Clone or download** this repository to your web server

2. **Configure database** - Edit `config/db.php`:
   ```php
   $host = 'localhost';
   $dbname = 'your_database';
   $user = 'your_username';
   $pass = 'your_password';
   ```

3. **Run setup** - Visit `http://yoursite.com/setup.php` to:
   - Initialize database schema
   - Create necessary columns
   - Add demo accounts and jobs

4. **Start using** - Visit `http://yoursite.com/`

### Demo Accounts
After running setup.php:
| Role | Email | Password |
|------|-------|----------|
| Employer | demo@company.com | demo123 |
| Job Seeker | seeker@demo.com | demo123 |

## 📁 Project Structure

```
Matcha-Production/
├── api/                    # REST API endpoints
│   ├── auth.php           # Authentication
│   ├── jobs.php           # Job operations
│   └── matches.php        # Match operations
├── assets/
│   ├── css/style.css      # CSS framework
│   ├── js/app.js          # Main app logic
│   └── js/swipe.js        # Swipe functionality
├── business/              # Employer pages
│   ├── dashboard.php
│   ├── jobs.php
│   ├── job-edit.php
│   └── candidates.php
├── includes/              # Shared components
│   ├── header.php
│   ├── footer.php
│   └── nav.php
├── config/db.php          # Database config
├── index.php              # Welcome page
├── login.php              # Login
├── register.php           # Registration
├── feed.php               # Swipe feed
├── matches.php            # Matches list
├── profile.php            # User profile
├── chat.php               # Chat interface
└── setup.php              # Database setup
```

## 🎨 Design System

The CSS framework includes:
- **RTL Support** - Full Hebrew language support
- **Mobile First** - Responsive design for all devices
- **Theme Variables** - Easy customization
- **Animations** - Smooth transitions and micro-interactions

### Key Variables
```css
--primary: #2ECC71;      /* Matcha Green */
--secondary: #2C3E50;    /* Deep Blue Gray */
--like: #2ECC71;         /* Swipe Right */
--nope: #E74C3C;         /* Swipe Left */
```

## 🔒 Security

- Password hashing with `password_hash()`
- Session-based authentication
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`

## 📝 License

MIT License - feel free to use this for your own projects!

---

<p align="center">
  Made with 💚 for job seekers and employers
</p>
