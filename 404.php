<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10B981">
    <title>404 - הדף לא נמצא | Matcha</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .error-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: var(--spacing-xl);
            text-align: center;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #f0f9ff 100%);
        }

        .error-illustration {
            position: relative;
            margin-bottom: var(--spacing-xl);
        }

        .error-number {
            font-size: 8rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
            opacity: 0.15;
            user-select: none;
        }

        .error-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 30px rgba(16, 185, 129, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        .error-icon svg {
            width: 36px;
            height: 36px;
            color: white;
        }

        @keyframes float {
            0%, 100% { transform: translate(-50%, -50%) translateY(0px); }
            50% { transform: translate(-50%, -50%) translateY(-10px); }
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: var(--spacing-sm);
        }

        .error-description {
            font-size: 1rem;
            color: var(--text-muted);
            max-width: 400px;
            line-height: 1.6;
            margin-bottom: var(--spacing-xl);
        }

        .error-actions {
            display: flex;
            gap: var(--spacing-md);
            flex-wrap: wrap;
            justify-content: center;
        }

        .error-actions .btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            font-size: 0.9375rem;
            font-weight: 600;
            border-radius: var(--radius-full);
            text-decoration: none;
            transition: all 0.2s;
        }

        .error-actions .btn svg {
            width: 18px;
            height: 18px;
        }

        .btn-back {
            background: var(--surface);
            color: var(--text-main);
            border: 1px solid var(--border);
        }

        .btn-back:hover {
            background: var(--background);
            border-color: var(--text-muted);
        }

        .error-footer {
            margin-top: 3rem;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .error-footer img {
            width: 24px;
            height: 24px;
            border-radius: 6px;
        }
    </style>
</head>

<body>
    <div class="error-page">
        <div class="error-illustration">
            <div class="error-number">404</div>
            <div class="error-icon">
                <i data-feather="search"></i>
            </div>
        </div>

        <h1 class="error-title">אופס! הדף לא נמצא</h1>
        <p class="error-description">
            נראה שהדף שחיפשתם לא קיים או שהועבר למקום אחר.
            אל דאגה, אפשר לחזור לדף הבית ולהמשיך לחפש את ההתאמה המושלמת.
        </p>

        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i data-feather="home"></i>
                לדף הבית
            </a>
            <a href="javascript:history.back()" class="btn btn-back">
                <i data-feather="arrow-right"></i>
                חזרה אחורה
            </a>
        </div>

        <div class="error-footer">
            <img src="/assets/images/ICON.jpeg" alt="Matcha">
            <span>Matcha - מצא את העבודה הבאה שלך</span>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>
