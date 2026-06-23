<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

$error = null;
$next = safe_next_path((string) ($_GET['next'] ?? $_POST['next'] ?? 'dashboard.php'));

if (staff_is_logged_in()) {
    header('Location: ' . ($next !== '' ? $next : 'dashboard.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (hash_equals(STAFF_USERNAME, $username) && hash_equals(STAFF_PASSWORD, $password)) {
        session_regenerate_id(true);
        $_SESSION['staff_logged_in'] = true;
        $_SESSION['staff_username'] = $username;

        header('Location: ' . ($next !== '' ? $next : 'dashboard.php'));
        exit;
    }

    $error = 'Invalid staff username or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Login - <?= e(SCHOOL_NAME) ?></title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <main class="shell narrow">
        <header class="topbar">
            <h1 class="brand"><?= e(SCHOOL_NAME) ?></h1>
            <nav class="nav" aria-label="Login navigation">
                <a class="secondary" href="index.php">Back to Home</a>
            </nav>
        </header>

        <section class="panel auth-panel">
            <h2>Staff Login</h2>

            <?php if ($error !== null): ?>
                <p class="error"><?= e($error) ?></p>
            <?php endif; ?>

            <form class="form-grid" method="post" action="login.php">
                <input type="hidden" name="next" value="<?= e($next) ?>">

                <label>
                    Username
                    <input name="username" autocomplete="username" required>
                </label>

                <label>
                    Password
                    <input type="password" name="password" autocomplete="current-password" required>
                </label>

                <button class="button" type="submit">Log In</button>
            </form>
        </section>
    </main>
</body>
</html>

