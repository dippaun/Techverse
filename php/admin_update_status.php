<?php
require_once __DIR__ . '/admin_guard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$data = readJsonBody();
$id = (int) ($data['id'] ?? 0);
$status = $data['status'] ?? '';

if ($id <= 0 || !in_array($status, ['pending', 'verified'], true)) {
    jsonResponse(['success' => false, 'message' => 'Invalid request.'], 422);
}

$pdo = getDb();
$stmt = $pdo->prepare('UPDATE students SET status = ? WHERE id = ?');
$stmt->execute([$status, $id]);

jsonResponse(['success' => true]);
