<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('manager');
require_once __DIR__ . '/../config/db.php';
$stats = [
 'total' => (int)$pdo->query('SELECT COUNT(*) FROM tasks')->fetchColumn(),
 'completed' => (int)$pdo->query("SELECT COUNT(*) FROM tasks WHERE status='completed'")->fetchColumn(),
 'pending' => (int)$pdo->query("SELECT COUNT(*) FROM tasks WHERE status <> 'completed'")->fetchColumn(),
 'overdue' => (int)$pdo->query("SELECT COUNT(*) FROM tasks WHERE status <> 'completed' AND due_date < CURDATE()")->fetchColumn()
];
$pageTitle = 'Manager Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>
<h1>Manager Dashboard</h1>
<div class="grid">
<?php foreach (['Total tasks'=>'total','Completed'=>'completed','Pending / in progress'=>'pending','Overdue'=>'overdue'] as $label=>$key): ?>
<section class="panel"><p class="muted"><?= htmlspecialchars($label) ?></p><p class="stat"><?= $stats[$key] ?></p></section>
<?php endforeach; ?>
</div>
<nav class="quick-links"><a href="tasks.php">Manage tasks</a><a href="reviews.php">Performance reviews</a><a href="reports.php">Daily Work Reports</a><a href="../auth/logout.php">Log out</a></nav>
</main></body></html>
