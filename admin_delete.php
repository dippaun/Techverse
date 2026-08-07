<?php
require_once __DIR__ . '/admin_guard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$data = readJsonBody();
$id = (int) ($data['id'] ?? 0);

if ($id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid request.'], 422);
}

$pdo = getDb();
$stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
$stmt->execute([$id]);

jsonResponse(['success' => true]);
