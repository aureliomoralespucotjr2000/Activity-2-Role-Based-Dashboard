<?php
require __DIR__ . '/../auth.php';
require_role('admin', 'instructor');         // admin + instructor only

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        http_response_code(403);
        exit('403 Forbidden: invalid token.');
    }
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $subject    = trim($_POST['subject'] ?? '');
    $grade      = filter_var($_POST['grade'] ?? '', FILTER_VALIDATE_FLOAT);

    if (!$student_id || $subject === '' || strlen($subject) > 50 ||
        $grade === false || $grade < 0 || $grade > 100) {
        $err = 'Please enter a student, a subject (max 50 characters), and a grade from 0 to 100.';
    } else {
        // The chosen ID must really be a student
        $chk = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'student'");
        $chk->bind_param('i', $student_id);
        $chk->execute();
        if (!$chk->get_result()->fetch_assoc()) {
            $err = 'Invalid student.';
        } else {
            $by  = (int) $_SESSION['user_id'];
            $ins = $conn->prepare('INSERT INTO grades (student_id, subject, grade, encoded_by) VALUES (?, ?, ?, ?)');
            $ins->bind_param('isdi', $student_id, $subject, $grade, $by);
            $ins->execute();
            $msg = 'Grade saved.';
        }
    }
}

$students = $conn->query("SELECT id, username FROM users WHERE role = 'student' ORDER BY username")
                 ->fetch_all(MYSQLI_ASSOC);
$recent = $conn->query(
    'SELECT u.username, g.subject, g.grade, g.created_at
     FROM grades g JOIN users u ON u.id = g.student_id
     ORDER BY g.id DESC LIMIT 15'
)->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Encode grades';
$pageLabel = 'instructors and admin';
$wide      = true;
require __DIR__ . '/../includes/header.php';
?>
      <?php if ($msg): ?><p class="msg ok"><?= e($msg) ?></p><?php endif; ?>
      <?php if ($err): ?><p class="msg error"><?= e($err) ?></p><?php endif; ?>

      <form class="form" method="post" action="encode_grades.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label class="prompt">
          <span class="ps">student:</span>
          <select name="student_id" required>
            <?php foreach ($students as $s): ?>
              <option value="<?= (int) $s['id'] ?>"><?= e($s['username']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="prompt">
          <span class="ps">subject:</span>
          <input type="text" name="subject" maxlength="50" required spellcheck="false">
        </label>
        <label class="prompt">
          <span class="ps">grade:</span>
          <input type="number" name="grade" min="0" max="100" step="0.01" placeholder="0 - 100" required>
        </label>
        <button class="go" type="submit">Save grade</button>
      </form>

      <h2>Recently encoded</h2>
      <div class="tablewrap">
        <table>
          <tr><th>STUDENT</th><th>SUBJECT</th><th>GRADE</th><th>DATE</th></tr>
          <?php foreach ($recent as $r): ?>
          <tr>
            <td><?= e($r['username']) ?></td>
            <td><?= e($r['subject']) ?></td>
            <td><?= e(number_format((float) $r['grade'], 2)) ?></td>
            <td><?= e($r['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
<?php require __DIR__ . '/../includes/footer.php';
