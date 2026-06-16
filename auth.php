<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function staff_is_logged_in(): bool
{
    return isset($_SESSION['staff_logged_in']) && $_SESSION['staff_logged_in'] === true;
}

function safe_next_path(string $next): string
{
    if ($next === '' || preg_match('/^https?:\/\//i', $next) || substr($next, 0, 2) === '//') {
        return 'dashboard.php';
    }

    return $next;
}

function require_staff_login(): void
{
    if (staff_is_logged_in()) {
        return;
    }

    $next = safe_next_path($_SERVER['REQUEST_URI'] ?? 'checkin.php');
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

    if (strpos($accept, 'application/json') !== false || $_SERVER['REQUEST_METHOD'] === 'POST') {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'unauthorized',
            'message' => 'Please log in before scanning student passes.',
        ]);
        exit;
    }

    header('Location: login.php?next=' . rawurlencode($next));
    exit;
}

