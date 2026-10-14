
<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('manager');
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assignedTo = (int) ($_POST['assigned_to'] ?? 0);
    $priority = $_POST['priority'] ?? 'medium';
    $dueDate = $_POST['due_date'] ?? '';

    if (
        $title === '' ||
        $assignedTo < 1 ||
        !in_array($priority, ['low', 'medium', 'high'], true) ||
        $dueDate === ''
    ) {
        $error = 'Please fill all required fields.';
    } else {
        $check = $pdo->prepare(
            "SELECT id FROM users WHERE id = ? AND role = 'employee'"
        );
        $check->execute([$assignedTo]);

        if (!$check->fetch()) {
            $error = 'Please select a valid employee.';
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO tasks
                (title, description, assigned_to, assigned_by, priority, due_date)
                VALUES (?, ?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $title,
                $description,
                $assignedTo,
                $_SESSION['user_id'],
                $priority,
                $dueDate
            ]);

            $success = 'Task assigned successfully!';
        }
    }
}

$employees = $pdo->query(
    "SELECT id, name FROM users
     WHERE role = 'employee'
     ORDER BY name"
)->fetchAll();

$tasks = $pdo->query(
    "SELECT t.*, e.name AS employee_name
     FROM tasks t
     JOIN users e ON e.id = t.assigned_to
     ORDER BY t.id DESC"
)->fetchAll();

$pageTitle = 'Manage Tasks';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Manage Tasks</h1>

<section class="panel">
    <h2>Assign New Task</h2>

    <?php if ($error): ?>
        <p class="alert"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!$employees): ?>
        <p>Create an employee account before assigning a task.</p>
    <?php else: ?>
    <form method="post">
        <label for="title">Task title</label>
        <input id="title" name="title" required maxlength="200">

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="3"></textarea>

        <label for="assigned_to">Assign to employee</label>
        <select id="assigned_to" name="assigned_to" required>
            <option value="">Select employee</option>
            <?php foreach ($employees as $employee): ?>
                <option value="<?= (int) $employee['id'] ?>">
                    <?= htmlspecialchars($employee['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="priority">Priority</label>
        <select id="priority" name="priority">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
        </select>

        <label for="due_date">Due date</label>
        <input id="due_date" type="date" name="due_date"
               min="<?= date('Y-m-d') ?>" required>

        <button type="submit">Assign Task</button>
    </form>
    <?php endif; ?>
</section>

<section class="panel">
    <h2>Assigned Tasks</h2>

    <?php if (!$tasks): ?>
        <p>No tasks assigned yet.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Employee</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= htmlspecialchars($task['employee_name']) ?></td>
                    <td><?= htmlspecialchars($task['priority']) ?></td>
                    <td><?= htmlspecialchars($task['status']) ?></td>
                    <td><?= htmlspecialchars($task['due_date']) ?></td>
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
