<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        header('Location: ' . ($user['role'] === 'manager'
            ? '../manager/dashboard.php'
            : '../employee/dashboard.php'));
        exit;
    }

    $error = 'Invalid email or password.';
}

$pageTitle = 'Login | Employee Work System';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="login-shell">
    <div class="login-showcase">
        <a class="login-brand" href="../index.php">
            <span class="brand-mark" aria-hidden="true">✦</span>
            <span>Employee Work System</span>
        </a>

        <div class="login-eyebrow"><span></span> WORK SMARTER</div>
        <h1 class="showcase-title">Employee Work <strong>System</strong></h1>
        <p class="showcase-copy">Manage tasks, track progress, and build a more productive team.</p>

        <div class="feature-grid">
            <div class="feature-item">
                <span class="feature-icon feature-blue">✓</span>
                <div><strong>Task Management</strong><small>Assign and track tasks efficiently</small></div>
            </div>
            <div class="feature-item">
                <span class="feature-icon feature-violet">▥</span>
                <div><strong>Daily Updates</strong><small>Track completed and pending work</small></div>
            </div>
            <div class="feature-item">
                <span class="feature-icon feature-green">♧</span>
                <div><strong>Performance Review</strong><small>Recognize progress and results</small></div>
            </div>
            <div class="feature-item">
                <span class="feature-icon feature-orange">▤</span>
                <div><strong>Reports &amp; Analytics</strong><small>Get a clearer view of team work</small></div>
            </div>
        </div>

        <div class="mini-dashboard" aria-label="Illustration of the employee dashboard">
            <aside class="mini-sidebar">
                <span class="mini-logo">✦</span>
                <span class="mini-active">⌂ &nbsp; Dashboard</span>
                <span>☷ &nbsp; Tasks</span>
                <span>▥ &nbsp; Reports</span>
                <span>♙ &nbsp; Team</span>
            </aside>
            <div class="mini-content">
                <div class="mini-topline"><strong>Team overview</strong><span>•••</span></div>
                <div class="mini-stats">
                    <div><small>Total tasks</small><b>24</b></div>
                    <div><small>Completed</small><b class="green-text">18</b></div>
                    <div><small>Pending</small><b class="orange-text">4</b></div>
                </div>
                <div class="mini-chart-row">
                    <div class="mini-bars">
                        <strong>Weekly progress</strong>
                        <div class="bars"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
                    </div>
                    <div class="mini-donut" aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <p class="showcase-foot"><span>✦</span> Small steps. Clear goals. Better teamwork.</p>
    </div>

    <div class="login-side">
        <div class="login-topline">TRACK <span>•</span> MANAGE <span>•</span> GROW TOGETHER</div>
        <div class="login-card">
            <div class="login-avatar" aria-hidden="true">♧</div>
            <h2>Welcome Back</h2>
            <p class="login-subtitle">Login to your Employee Work System account</p>

            <?php if ($error): ?>
                <p class="alert" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <form method="post" class="login-form">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <span aria-hidden="true">✉</span>
                    <input id="email" type="email" name="email" required autocomplete="username" placeholder="Enter your email">
                </div>

                <label for="password">Password</label>
                <div class="input-wrap">
                    <span aria-hidden="true">♙</span>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                </div>

                <button type="submit" class="login-submit"><span aria-hidden="true">↪</span> Sign In</button>
            </form>

            <div class="login-notice">
                <span aria-hidden="true">i</span>
                <p>Create a user account in the database before logging in.</p>
            </div>
            <div class="login-divider"><span></span><small>OR</small><span></span></div>
            <p class="login-bottom-note">Your team’s work, all in one place.</p>
        </div>
        <div class="login-copyright">EMPLOYEE WORK SYSTEM <span>•</span> PRODUCTIVITY MADE SIMPLE</div>
    </div>
</section>

</main>
</body>
</html>
