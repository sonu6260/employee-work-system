
<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('employee');
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = ($_POST['task_id'] ?? '') !== ''
        ? (int) $_POST['task_id']
        : null;

    $workDone = trim($_POST['work_done'] ?? '');
    $pendingWork = trim($_POST['pending_work'] ?? '');
    $hours = filter_var(
        $_POST['hours_worked'] ?? '',
        FILTER_VALIDATE_FLOAT
    );

    if (
        $workDone === '' ||
        $hours === false ||
        $hours < 0 ||
        $hours > 24
    ) {
        $error = 'Enter completed work and valid hours between 0 and 24.';
    } else {
        if ($taskId !== null) {
            $check = $pdo->prepare(
                'SELECT id FROM tasks WHERE id = ? AND assigned_to = ?'
            );
            $check->execute([$taskId, $_SESSION['user_id']]);

            if (!$check->fetch()) {
                $error = 'Please select a task assigned to you.';
            }
        }

        if ($error === '') {
            $stmt = $pdo->prepare(
                'INSERT INTO daily_updates
                (employee_id, task_id, update_date, work_done,
                 pending_work, hours_worked)
                VALUES (?, ?, CURDATE(), ?, ?, ?)'
            );

            $stmt->execute([
                $_SESSION['user_id'],
                $taskId,
                $workDone,
                $pendingWork,
                $hours
            ]);

            $success = 'Daily work update submitted successfully!';
        }
    }
}

$stmt = $pdo->prepare(
    'SELECT id, title FROM tasks
     WHERE assigned_to = ?
     ORDER BY due_date ASC'
);
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll();

$pageTitle = 'Daily Work Update';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Daily Work Update</h1>

<?php if ($error): ?>
    <p class="alert"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<section class="panel">
    <form method="post">
        <label for="task_id">Related task</label>
        <select id="task_id" name="task_id">
            <option value="">General work update</option>
            <?php foreach ($tasks as $task): ?>
                <option value="<?= (int) $task['id'] ?>">
                    <?= htmlspecialchars($task['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="work_done">Work completed today</label>
        <textarea id="work_done" name="work_done"
                  rows="4" required></textarea>

        <label for="pending_work">Pending work / blockers</label>
        <textarea id="pending_work" name="pending_work"
                  rows="3"></textarea>

        <label for="hours_worked">Hours worked</label>
        <input id="hours_worked" type="number"
               name="hours_worked" min="0" max="24"
               step="0.25" value="8" required>

        <button type="submit">Submit Daily Update</button>
    </form>
</section>

<p><a href="dashboard.php">Back to dashboard</a></p>

</main>
</body>
</html>
