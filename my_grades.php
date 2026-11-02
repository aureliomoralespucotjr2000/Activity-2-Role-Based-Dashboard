<?php
require __DIR__ . '/../auth.php';
require_role('student');                     // students only

// The ID comes from the trusted SESSION, never from the URL (prevents IDOR)
$student_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare('SELECT subject, grade, created_at FROM grades WHERE student_id = ? ORDER BY subject');
$stmt->bind_param('i', $student_id);
$stmt->execute();
$grades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$who = $conn->prepare('SELECT username FROM users WHERE id = ?');
$who->bind_param('i', $student_id);
$who->execute();
$me = $who->get_result()->fetch_assoc();

$pageTitle = 'My grades';
$pageLabel = 'student';
$wide      = true;
require __DIR__ . '/../includes/header.php';
?>
      <p class="info"><span>Student</span> <?= e($me['username']) ?></p>

      <?php if (!$grades): ?>
        <p class="muted">No grades have been encoded yet.</p>
      <?php else: ?>
      <div class="tablewrap">
        <table>
          <tr><th>SUBJECT</th><th>GRADE</th><th>DATE ENCODED</th></tr>
          <?php foreach ($grades as $g): ?>
          <tr>
            <td><?= e($g['subject']) ?></td>
            <td><?= e(number_format((float) $g['grade'], 2)) ?></td>
            <td><?= e($g['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
      <?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php';
