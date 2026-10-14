
<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('manager');
require_once __DIR__ . '/../config/db.php';

$date = $_GET['date'] ?? date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $date = date('Y-m-d');
}

$stmt = $pdo->prepare(
    'SELECT
        u.name AS employee_name,
        d.update_date,
        d.work_done,
        d.pending_work,
        d.hours_worked,
        t.title AS task_title
     FROM daily_updates d
     JOIN users u ON u.id = d.employee_id
     LEFT JOIN tasks t ON t.id = d.task_id
     WHERE d.update_date = ?
     ORDER BY u.name ASC, d.created_at DESC'
);

$stmt->execute([$date]);
$updates = $stmt->fetchAll();

$pageTitle = 'Daily Work Reports';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Daily Work Reports</h1>

<section class="panel">
    <form method="get">
        <label for="date">Select report date</label>
        <input type="date" id="date" name="date"
               value="<?= htmlspecialchars($date) ?>" required>
        <button type="submit">View Report</button>
    </form>
</section>

<section class="panel">
    <h2>Employee Updates — <?= htmlspecialchars($date) ?></h2>

    <?php if (!$updates): ?>
        <p>No work updates found for this date.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Task</th>
                        <th>Work Completed</th>
                        <th>Pending Work</th>
                        <th>Hours</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($updates as $update): ?>
                    <tr>
                        <td><?= htmlspecialchars($update['employee_name']) ?></td>
                        <td><?= htmlspecialchars($update['task_title'] ?? 'General update') ?></td>
                        <td><?= nl2br(htmlspecialchars($update['work_done'])) ?></td>
                        <td><?= nl2br(htmlspecialchars($update['pending_work'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string) $update['hours_worked']) ?></td>
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
