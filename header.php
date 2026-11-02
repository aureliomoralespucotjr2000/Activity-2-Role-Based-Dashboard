<?php
// includes/header.php - top of every protected page: <head>, role-based menu, page heading.
// Set these BEFORE including it:
//   $pageTitle  big heading       $pageLabel  small label above it
//   $wide       true for tables   $pageClass  '' or 'denied'
$pageTitle = $pageTitle ?? 'Dashboard';
$pageLabel = $pageLabel ?? '';
$wide      = $wide ?? false;
$pageClass = $pageClass ?? '';
$classes   = trim(($wide ? 'wide ' : '') . $pageClass);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($pageTitle) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Onest:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body class="panel-page">
  <main class="term<?= $classes !== '' ? ' ' . e($classes) : '' ?>" id="term">
    <section class="body">

<?php if (!empty($_SESSION['role'])): ?>
      <?php // Menu: each link is shown ONLY to the roles that may open it.
            // This is for usability only. The real lock is require_role() on each page. ?>
      <nav class="menu">
        <a href="<?= BASE_URL ?>/dashboard.php">Home</a>

        <?php if (has_role('admin')): ?>
          <a href="<?= BASE_URL ?>/admin/manage_users.php">Manage users</a>
        <?php endif; ?>

        <?php if (has_role('admin', 'instructor')): ?>
          <a href="<?= BASE_URL ?>/instructor/encode_grades.php">Encode grades</a>
        <?php endif; ?>

        <?php if (has_role('student')): ?>
          <a href="<?= BASE_URL ?>/student/my_grades.php">My grades</a>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>/logout.php" class="inline">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <button type="submit" class="link">Log out (<?= e($_SESSION['role']) ?>)</button>
        </form>
      </nav>
<?php endif; ?>

      <?php if ($pageLabel !== ''): ?><p class="eyebrow"><?= e($pageLabel) ?></p><?php endif; ?>
      <h1 class="glitch"><?= e($pageTitle) ?></h1>
