<!-- Legal Footer -->
<footer style="padding: var(--spacing-md) var(--spacing-lg); border-top: 1px solid var(--border); text-align: center; margin-bottom: 70px;">
    <div style="display: flex; justify-content: center; gap: var(--spacing-lg); flex-wrap: wrap; margin-bottom: var(--spacing-sm);">
        <a href="/terms.php" style="font-size: 0.75rem; color: var(--text-light); text-decoration: none;">תנאי שימוש</a>
        <a href="/privacy.php" style="font-size: 0.75rem; color: var(--text-light); text-decoration: none;">מדיניות פרטיות</a>
        <a href="/accessibility.php" style="font-size: 0.75rem; color: var(--text-light); text-decoration: none;">נגישות</a>
    </div>
    <p style="font-size: 0.7rem; color: var(--text-light);">&copy; <?php echo date('Y'); ?> Matcha. כל הזכויות שמורות.</p>
</footer>
</div><!-- /.app-container -->

<script src="/assets/js/app.js"></script>
<script>
    // Initialize Feather Icons
    feather.replace();
</script>
<?php if (isset($additionalScripts)): ?>
    <?php foreach ($additionalScripts as $script): ?>
        <script src="<?php echo $script; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>

</html>