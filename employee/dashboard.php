<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('employee');
require_once __DIR__ . '/../config/db.php';
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to=? AND status <> 'completed'");
$stmt->execute([$_SESSION['user_id']]); $openTasks = (int)$stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to=? AND status <> 'completed' AND due_date < CURDATE()");
$stmt->execute([$_SESSION['user_id']]); $overdueTasks = (int)$stmt->fetchColumn();
$pageTitle = 'Employee Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>
<h1>Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Employee', ENT_QUOTES, 'UTF-8') ?></h1>
<div class="grid">
<section class="panel"><p class="muted">Open tasks</p><p class="stat"><?= $openTasks ?></p></section>
<section class="panel"><p class="muted">Overdue tasks</p><p class="stat"><?= $overdueTasks ?></p></section>
</div>
<nav class="quick-links"><a href="my-tasks.php">My tasks</a><a href="daily-update.php">Submit daily update</a><a href="../auth/logout.php">Log out</a></nav>
</main></body></html>
