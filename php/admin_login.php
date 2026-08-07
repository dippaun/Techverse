<?php
session_start();
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$data = readJsonBody();
$username = trim($data['username'] ?? '');
$password = (string) ($data['password'] ?? '');

if ($username === '' || $password === '') {
    jsonResponse(['success' => false, 'message' => 'Enter username and password.'], 422);
}

$pdo = getDb();
$stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
$admin = $stmt->fetch();

if (!$admin) {
    jsonResponse(['success' => false, 'message' => 'Incorrect username or password.'], 401);
}

// Your admins table currently stores a plain-text password (e.g. "Admin@techverse2026").
// This checks a hashed password first, then falls back to a plain-text match so your
// existing row keeps working. Once you reset the password through a hashed flow, the
// plain-text fallback stops being needed — see the note at the bottom of this file.
$isValid = password_verify($password, $admin['password']) || hash_equals($admin['password'], $password);

if (!$isValid) {
    jsonResponse(['success' => false, 'message' => 'Incorrect username or password.'], 401);
}

$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_username'] = $admin['username'];

jsonResponse(['success' => true]);

/*
 * To switch your existing admin row to a proper hashed password, run this once
 * in phpMyAdmin's SQL tab (replace the value with a hash you generate via
 * PHP's password_hash('yourpassword', PASSWORD_BCRYPT)):
 *
 * UPDATE admins SET password = '$2y$10$...your generated hash...' WHERE username = 'admin';
 */
