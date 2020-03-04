<div class="top-menu">
    <div class="menu-options">
        <?php if (isLoggedIn()): ?>
            Hello <?php echo htmlEscape(getAuthUser()) ?>
            <a href="logout.php">Log out</a>
        <?php else: ?>
            <a href="login.php">Log in</a>
        <?php endif; ?>
    </div>
</div>
<a href="index.php">
    <h1>爽log</h1>
</a>
<p>XIANG常人之所不能</p>
