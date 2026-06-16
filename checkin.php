<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

require_staff_login();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check In - <?= e(SCHOOL_NAME) ?></title>
    <link rel="stylesheet" href="assets/styles.css">
    <script src="https://unpkg.com/html5-qrcode" defer></script>
    <script src="assets/checkin.js" defer></script>
</head>
<body>
    <main class="shell">
        <header class="topbar">
            <h1 class="brand"><?= e(SCHOOL_NAME) ?></h1>
            <nav class="nav" aria-label="Main navigation">
                <a class="secondary" href="dashboard.php">Dashboard</a>`r`n                <a class="secondary" href="register.php">Register</a>
                <a href="checkin.php">Check In</a>
                <a class="secondary" href="logout.php">Log Out</a>
            </nav>
        </header>

        <section class="scanner-layout">
            <div class="panel">
                <h2>School Event Check-In</h2>
                <div id="reader" aria-label="Camera QR scanner"></div>
            </div>

            <div class="panel scan-status" aria-live="polite">
                <span id="statusBadge" class="status-badge">Waiting for QR Code</span>
                <p id="statusMessage" class="message">Point the camera at a student pass.</p>
                <dl id="studentDetails" class="details"></dl>
            </div>
        </section>
    </main>
</body>
</html>

