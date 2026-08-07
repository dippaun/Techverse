<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$data = readJsonBody();
$email = strtolower(trim($data['email'] ?? ''));
$password = (string) ($data['password'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    jsonResponse(['success' => false, 'message' => 'Enter your registered email and password.'], 422);
}

$pdo = getDb();
$stmt = $pdo->prepare('SELECT * FROM students WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$student = $stmt->fetch();

if (!$student) {
    jsonResponse(['success' => false, 'message' => 'No registration found for that email.'], 404);
}
if (!password_verify($password, $student['password'])) {
    jsonResponse(['success' => false, 'message' => 'Incorrect password.'], 401);
}

unset($student['password']);
$student['team_members'] = json_decode($student['team_members'] ?? '[]', true);

jsonResponse(['success' => true, 'student' => $student]);
