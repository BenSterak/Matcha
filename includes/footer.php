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