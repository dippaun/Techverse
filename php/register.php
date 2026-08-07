<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$data = readJsonBody();

$fullName     = trim($data['full_name'] ?? '');
$phone        = trim($data['phone'] ?? '');
$email        = strtolower(trim($data['email'] ?? ''));
$address      = trim($data['address'] ?? '');
$enrollmentNo = trim($data['enrollment_no'] ?? '');
$course       = trim($data['course'] ?? '');
$collegeName  = trim($data['college_name'] ?? '');
$arena        = trim($data['arena'] ?? '');
$teamMembers  = $data['team_members'] ?? [];
$paymentId    = trim($data['payment_id'] ?? '');
$password     = (string) ($data['password'] ?? '');

$validArenas = ['trading', 'robotics', 'drone', 'startup', 'coding', 'ai'];

$errors = [];
if ($fullName === '') $errors[] = 'full_name';
if (!preg_match('/^[0-9]{10}$/', preg_replace('/\D/', '', $phone))) $errors[] = 'phone';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'email';
if ($address === '') $errors[] = 'address';
if ($enrollmentNo === '') $errors[] = 'enrollment_no';
if ($course === '') $errors[] = 'course';
if ($collegeName === '') $errors[] = 'college_name';
if (!in_array($arena, $validArenas, true)) $errors[] = 'arena';
if ($paymentId === '') $errors[] = 'payment_id';
if (strlen($password) < 6) $errors[] = 'password';
if (!is_array($teamMembers)) $teamMembers = [];

if (!empty($errors)) {
    jsonResponse(['success' => false, 'message' => 'Missing or invalid fields.', 'fields' => $errors], 422);
}

$pdo = getDb();

// Prevent duplicate registrations for the same email
$check = $pdo->prepare('SELECT id FROM students WHERE email = ? LIMIT 1');
$check->execute([$email]);
if ($check->fetch()) {
    jsonResponse(['success' => false, 'message' => 'An account with this email is already registered.'], 409);
}

$teamId = 'TV26-' . strtoupper(substr($arena, 0, 3)) . '-' . random_int(1000, 9999);
$passwordHash = password_hash($password, PASSWORD_BCRYPT);
$teamMembersJson = json_encode(array_values(array_filter(array_map('trim', $teamMembers))));

$stmt = $pdo->prepare(
    'INSERT INTO students
        (full_name, email, phone, address, enrollment_no, course, college_name, arena, team_members, payment_id, team_id, password, status, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "pending", NOW())'
);

try {
    $stmt->execute([
        $fullName, $email, $phone, $address, $enrollmentNo, $course, $collegeName,
        $arena, $teamMembersJson, $paymentId, $teamId, $passwordHash
    ]);
} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Could not save registration. Please try again.'], 500);
}

jsonResponse([
    'success' => true,
    'student' => [
        'full_name' => $fullName, 'email' => $email, 'phone' => $phone, 'address' => $address,
        'enrollment_no' => $enrollmentNo, 'course' => $course, 'college_name' => $collegeName,
        'arena' => $arena, 'team_members' => json_decode($teamMembersJson, true), 'payment_id' => $paymentId,
        'team_id' => $teamId, 'status' => 'pending', 'created_at' => date('c')
    ]
]);