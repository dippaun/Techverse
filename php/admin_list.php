<?php
require_once __DIR__ . '/admin_guard.php';

$pdo = getDb();
$rows = $pdo->query(
    'SELECT id, full_name, email, phone, address, enrollment_no, course, arena,
            team_members, payment_id, team_id, status, created_at
     FROM students ORDER BY created_at DESC'
)->fetchAll();

foreach ($rows as &$r) {
    $r['team_members'] = json_decode($r['team_members'] ?? '[]', true);
}

jsonResponse(['success' => true, 'students' => $rows]);
