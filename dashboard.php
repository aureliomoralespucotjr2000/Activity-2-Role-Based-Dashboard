<?php
require __DIR__ . '/auth.php';
require_login();                            // replaces the Activity 1 check

$pageTitle = 'Welcome back';
$pageLabel = 'secure session';
$wide      = true;
require __DIR__ . '/includes/header.php';
?>
      <p class="info"><span>Username</span> <?= e($_SESSION['username'] ?? '') ?></p>
      <p class="info"><span>User ID</span> <?= (int) $_SESSION['user_id'] ?></p>
      <p class="info"><span>Role</span> <?= e($_SESSION['role']) ?></p>
      <p class="muted">Use the menu above. Pages you are not allowed to open are hidden, and the server blocks them even if you type the URL.</p>
<?php require __DIR__ . '/includes/footer.php'; ?>
