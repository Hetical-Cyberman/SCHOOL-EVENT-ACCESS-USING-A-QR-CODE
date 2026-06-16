<?php

declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

require_staff_login();

function normalize_qr_value(string $value): string
{
    $value = trim($value);

    if (filter_var($value, FILTER_VALIDATE_URL)) {
        $query = parse_url($value, PHP_URL_QUERY);
        if (is_string($query)) {
            parse_str($query, $params);
            if (isset($params['token']) && is_string($params['token'])) {
                return trim($params['token']);
            }
        }
    }

    return $value;
}

function format_student(array $registration): array
{
    return [
        'registration_id' => $registration['registration_id'],
        'student_name' => $registration['student_name'],
        'admission_no' => $registration['admission_no'],
        'class_name' => $registration['class_name'],
        'event_name' => $registration['event_name'],
        'checkin_time' => $registration['checkin_time'] !== null
            ? date('g:i A', strtotime((string) $registration['checkin_time']))
            : date('g:i A'),
    ];
}

$input = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? (string) ($_POST['qr'] ?? '')
    : (string) ($_GET['token'] ?? '');

$qrValue = normalize_qr_value($input);

if ($qrValue === '') {
    json_response([
        'status' => 'invalid',
        'message' => 'Access denied. No QR token was supplied.',
    ], 400);
}

$pdo = db();
$pdo->beginTransaction();

try {
    $statement = $pdo->prepare(
        'SELECT *
         FROM registrations
         WHERE qr_token = :qr_token OR registration_id = :registration_id
         LIMIT 1
         FOR UPDATE'
    );
    $statement->execute([
        'qr_token' => $qrValue,
        'registration_id' => $qrValue,
    ]);
    $registration = $statement->fetch();

    if (!$registration) {
        $pdo->commit();
        json_response([
            'status' => 'invalid',
            'message' => 'Access denied. This QR code is not registered for this event.',
        ], 404);
    }

    if ($registration['attendance_status'] === 'checked_in') {
        $pdo->commit();
        json_response([
            'status' => 'already_checked_in',
            'message' => 'This student has already checked in.',
            'student' => format_student($registration),
        ], 409);
    }

    $update = $pdo->prepare(
        "UPDATE registrations
         SET attendance_status = 'checked_in', checkin_time = NOW()
         WHERE id = :id"
    );
    $update->execute(['id' => $registration['id']]);

    $refresh = $pdo->prepare('SELECT * FROM registrations WHERE id = :id LIMIT 1');
    $refresh->execute(['id' => $registration['id']]);
    $registration = $refresh->fetch();

    $pdo->commit();

    json_response([
        'status' => 'valid',
        'message' => 'Access granted. Student may enter.',
        'student' => format_student($registration),
    ]);
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    json_response([
        'status' => 'error',
        'message' => 'Verification failed. Please try again.',
    ], 500);
}
