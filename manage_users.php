<?php
require __DIR__ . '/../auth.php';
require_role('admin');                       // admin only

const ROLES = ['admin', 'instructor', 'student'];
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        http_response_code(403);
        exit('403 Forbidden: invalid token.');
    }
    $uid  = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $role = $_POST['role'] ?? '';

    if (!$uid || !in_array($role, ROLES, true)) {
        $err = 'Invalid request.';
    } elseif ($uid === (int) $_SESSION['user_id']) {
        $err = 'You cannot change your own role.';
    } else {
        $stmt = $conn->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->bind_param('si', $role, $uid);
        $stmt->execute();
        $msg = 'Role updated.';
    }
}

$users = $conn->query('SELECT id, username, email, role, created_at FROM users ORDER BY id')
              ->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Manage users';
$pageLabel = 'admin only';
$wide      = true;
require __DIR__ . '/../includes/header.php';
?>
      <?php if ($msg): ?><p class="msg ok"><?= e($msg) ?></p><?php endif; ?>
      <?php if ($err): ?><p class="msg error"><?= e($err) ?></p><?php endif; ?>

      <div class="tablewrap">
        <table>
          <tr><th>ID</th><th>USERNAME</th><th>EMAIL</th><th>ROLE</th><th>CHANGE ROLE</th></tr>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><?= (int) $u['id'] ?></td>
            <td><?= e($u['username']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['role']) ?></td>
            <td>
              <?php if ((int) $u['id'] === (int) $_SESSION['user_id']): ?>
                <span class="muted">(you)</span>
              <?php else: ?>
                <form method="post" class="inline">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                  <select name="role">
                    <?php foreach (ROLES as $r): ?>
                      <option value="<?= $r ?>" <?= $r === $u['role'] ? 'selected' : '' ?>><?= $r ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="mini">Save</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
<?php require __DIR__ . '/../includes/footer.php';
