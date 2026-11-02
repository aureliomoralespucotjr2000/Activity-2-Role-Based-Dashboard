<?php
// 403.php - friendly "access denied" page (shown by require_role()).
// Short and polite, no technical details.
require_once __DIR__ . '/auth.php';
http_response_code(403);

$pageTitle = 'Access denied';
$pageLabel = '403';
$pageClass = 'denied';
require __DIR__ . '/includes/header.php';
?>
      <p class="msg error">Sorry, you don't have permission to open this page.</p>
      <p class="hint"><a href="<?= BASE_URL ?>/dashboard.php">Go back to dashboard</a></p>
<?php require __DIR__ . '/includes/footer.php'; ?>
