# School Event Access Using a QR Code

A lightweight PHP/MySQL event registration and entrance check-in system for secondary school events.

## What It Does

- Registers students for a school event.
- Generates a unique registration ID and secure QR token.
- Shows a QR pass that can be printed or displayed on a phone.
- Lets staff scan passes at `/checkin.php` using a phone camera.
- Records attendance and blocks duplicate check-ins.

## Setup

1. Create a MySQL database, for example:

   ```sql
   CREATE DATABASE school_event_access CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Import [database.sql](database.sql).

3. Copy [config.sample.php](config.sample.php) to `config.php`.

4. Update the database settings in `config.php`.

5. Change the staff login settings in `config.php`.

6. Serve the project with Apache, XAMPP, WAMP, Laragon, or PHP's built-in server:

   ```powershell
   php -S localhost:8000
   ```

7. Open:

   - `http://localhost:8000/register.php` to register students.
   - `http://localhost:8000/checkin.php` to scan QR codes.

## Scanner Notes

The check-in page uses the phone camera through the `html5-qrcode` JavaScript library. The QR code stores a secure random token, not a guessable student admission number.
