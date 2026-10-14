
<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('employee');
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = (int) ($_POST['task_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (!in_array($status, ['todo', 'in_progress', 'completed'], true)) {
        $error = 'Invalid task status.';
    } else {
        $stmt = $pdo->prepare(
            "UPDATE tasks
             SET status = ?,
                 completed_at = CASE
                     WHEN ? = 'completed' THEN NOW()
                     ELSE NULL
                 END
             WHERE id = ? AND assigned_to = ?"
        );

        $stmt->execute([
            $status,
            $status,
            $taskId,
            $_SESSION['user_id']
        ]);

        if ($stmt->rowCount() > 0) {
            $success = 'Task status updated successfully!';
        } else {
            $check = $pdo->prepare(
                'SELECT id FROM tasks WHERE id = ? AND assigned_to = ?'
            );
            $check->execute([$taskId, $_SESSION['user_id']]);

            $success = $check->fetch()
                ? 'Task status is already up to date.'
                : '';
            if (!$check->fetch() && !$success) {
                $error = 'Task not found.';
            }
        }
    }
}

$stmt = $pdo->prepare(
    'SELECT id, title, description, priority, status, due_date
     FROM tasks
     WHERE assigned_to = ?
     ORDER BY due_date ASC'
);
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll();

$pageTitle = 'My Tasks';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>My Tasks</h1>

<?php if ($error): ?>
    <p class="alert"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<section class="panel">
    <?php if (!$tasks): ?>
        <p>No tasks assigned yet.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Priority</th>
                    <th>Due date</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($task['title']) ?></strong>
                        <br>
                        <?= nl2br(htmlspecialchars($task['description'] ?? '')) ?>
                    </td>
                    <td><?= htmlspecialchars($task['priority']) ?></td>
                    <td>
                        <?= htmlspecialchars($task['due_date']) ?>
                        <?php if (
                            $task['status'] !== 'completed' &&
                            $task['due_date'] < date('Y-m-d')
                        ): ?>
                            <strong> (Overdue)</strong>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($task['status']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="task_id"
                                   value="<?= (int) $task['id'] ?>">

                            <select name="status" required>
                                <option value="todo"
                                    <?= $task['status'] === 'todo' ? 'selected' : '' ?>>
                                    To Do
                                </option>
                                <option value="in_progress"
                                    <?= $task['status'] === 'in_progress' ? 'selected' : '' ?>>
                                    In Progress
                                </option>
                                <option value="completed"
                                    <?= $task['status'] === 'completed' ? 'selected' : '' ?>>
                                    Completed
                                </option>
                            </select>

                            <button type="submit">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>

<p><a href="dashboard.php">Back to dashboard</a></p>
</main>
</body>
</html>
