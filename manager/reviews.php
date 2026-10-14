
<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('manager');
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = (int) ($_POST['employee_id'] ?? 0);
    $startDate = $_POST['review_period_start'] ?? '';
    $endDate = $_POST['review_period_end'] ?? '';
    $completion = filter_var($_POST['completion_rating'] ?? '', FILTER_VALIDATE_INT);
    $punctuality = filter_var($_POST['punctuality_rating'] ?? '', FILTER_VALIDATE_INT);
    $quality = filter_var($_POST['work_quality_rating'] ?? '', FILTER_VALIDATE_INT);
    $comments = trim($_POST['comments'] ?? '');

    if (
        $employeeId < 1 ||
        !$startDate ||
        !$endDate ||
        $startDate > $endDate ||
        !in_array($completion, [1, 2, 3, 4, 5], true) ||
        !in_array($punctuality, [1, 2, 3, 4, 5], true) ||
        !in_array($quality, [1, 2, 3, 4, 5], true)
    ) {
        $error = 'Please enter valid review details and ratings from 1 to 5.';
    } else {
        $check = $pdo->prepare(
            "SELECT id FROM users WHERE id = ? AND role = 'employee'"
        );
        $check->execute([$employeeId]);

        if (!$check->fetch()) {
            $error = 'Please select a valid employee.';
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO performance_reviews
                (employee_id, manager_id, review_period_start,
                 review_period_end, completion_rating,
                 punctuality_rating, work_quality_rating, comments)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $employeeId,
                $_SESSION['user_id'],
                $startDate,
                $endDate,
                $completion,
                $punctuality,
                $quality,
                $comments
            ]);

            $success = 'Performance review saved successfully!';
        }
    }
}

$employees = $pdo->query(
    "SELECT id, name FROM users
     WHERE role = 'employee' ORDER BY name"
)->fetchAll();

$reviews = $pdo->query(
    'SELECT r.*, e.name AS employee_name, m.name AS manager_name
     FROM performance_reviews r
     JOIN users e ON e.id = r.employee_id
     JOIN users m ON m.id = r.manager_id
     ORDER BY r.created_at DESC'
)->fetchAll();

$pageTitle = 'Performance Reviews';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Performance Evaluation</h1>

<section class="panel">
    <h2>Create Review</h2>

    <?php if ($error): ?>
        <p class="alert"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!$employees): ?>
        <p>No employees found.</p>
    <?php else: ?>
    <form method="post">
        <label for="employee_id">Employee</label>
        <select id="employee_id" name="employee_id" required>
            <option value="">Select employee</option>
            <?php foreach ($employees as $employee): ?>
                <option value="<?= (int) $employee['id'] ?>">
                    <?= htmlspecialchars($employee['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="review_period_start">Review period start</label>
        <input type="date" id="review_period_start"
               name="review_period_start" required>

        <label for="review_period_end">Review period end</label>
        <input type="date" id="review_period_end"
               name="review_period_end" required>

        <label for="completion_rating">Task completion (1–5)</label>
        <select id="completion_rating" name="completion_rating" required>
            <option value="">Select rating</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label for="punctuality_rating">Punctuality (1–5)</label>
        <select id="punctuality_rating" name="punctuality_rating" required>
            <option value="">Select rating</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label for="work_quality_rating">Work quality (1–5)</label>
        <select id="work_quality_rating" name="work_quality_rating" required>
            <option value="">Select rating</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label for="comments">Manager comments</label>
        <textarea id="comments" name="comments" rows="4"></textarea>

        <button type="submit">Save Review</button>
    </form>
    <?php endif; ?>
</section>

<section class="panel">
    <h2>Previous Reviews</h2>

    <?php if (!$reviews): ?>
        <p>No performance reviews yet.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Review Period</th>
                    <th>Completion</th>
                    <th>Punctuality</th>
                    <th>Quality</th>
                    <th>Comments</th>
                    <th>Overall Score</th> 
                   
                </tr>
            </thead>
            <tbody>
            <?php foreach ($reviews as $review): ?>
                <tr>
                    <td><?= htmlspecialchars($review['employee_name']) ?></td>
                    <td>
                        <?= htmlspecialchars($review['review_period_start']) ?>
                        to
                        <?= htmlspecialchars($review['review_period_end']) ?>
                    </td>
                    <td><?= (int) $review['completion_rating'] ?>/5</td>
                    <td><?= (int) $review['punctuality_rating'] ?>/5</td>
                    <td><?= (int) $review['work_quality_rating'] ?>/5</td>
                    <td><?= nl2br(htmlspecialchars($review['comments'] ?? '')) ?></td>
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
