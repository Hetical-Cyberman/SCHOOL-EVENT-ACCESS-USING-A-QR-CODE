<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';

require_staff_login();

$pdo = db();
ensure_app_schema($pdo);
$saved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statement = $pdo->prepare(
        'UPDATE event_settings
         SET event_name = :event_name,
             event_date = :event_date,
             event_time = :event_time,
             venue = :venue,
             announcement = :announcement
         WHERE id = 1'
    );
    $statement->execute([
        'event_name' => trim((string) ($_POST['event_name'] ?? '')),
        'event_date' => trim((string) ($_POST['event_date'] ?? '')) ?: null,
        'event_time' => trim((string) ($_POST['event_time'] ?? '')),
        'venue' => trim((string) ($_POST['venue'] ?? '')),
        'announcement' => trim((string) ($_POST['announcement'] ?? '')),
    ]);
    $saved = true;
}

$event = get_event_settings($pdo);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event Info - <?= e(SCHOOL_NAME) ?></title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <main class="shell">
        <header class="topbar">
            <h1 class="brand">Event Information</h1>
            <nav class="nav" aria-label="Main navigation">
                <a class="secondary" href="dashboard.php">Dashboard</a>
                <a href="event.php">Event Info</a>
                <a class="secondary" href="logout.php">Log Out</a>
            </nav>
        </header>

        <section class="panel">
            <h2>Details and Announcement</h2>
            <?php if ($saved): ?><p class="success-note">Event information saved.</p><?php endif; ?>
            <form class="form-grid" method="post" action="event.php">
                <label>Event Name <input name="event_name" value="<?= e($event['event_name']) ?>" required></label>
                <label>Event Date <input type="date" name="event_date" value="<?= e($event['event_date']) ?>"></label>
                <label>Event Time <input name="event_time" value="<?= e($event['event_time']) ?>" placeholder="9:00 AM"></label>
                <label>Venue <input name="venue" value="<?= e($event['venue']) ?>" placeholder="School Hall"></label>
                <label>Announcement <textarea name="announcement" rows="5"><?= e($event['announcement']) ?></textarea></label>
                <button class="button" type="submit">Save Event Info</button>
            </form>
        </section>
    </main>
</body>
</html>
