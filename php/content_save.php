<?php
/**
 * content_save.php
 * Admin-only write endpoint for the Site Content tab. Expects a POST body
 * of { "section": "competitions", "data": [...] } and overwrites just that
 * section in content.json.
 *
 * IMPORTANT: this uses the same session check pattern as your other
 * admin_*.php files (admin_update_status.php, admin_delete.php, etc).
 * If those check a different session key than admin_logged_in below,
 * update the check on the marked line to match — otherwise this endpoint
 * will either reject your logged-in admin, or (worse) accept writes from
 * anyone. Don't ship this file until that line matches your real auth.
 */

session_start();
header('Content-Type: application/json');

// ---- auth check: make this match admin_login.php's session logic ----
if (empty($_SESSION['admin_logged_in'])) {
  http_response_code(401);
  echo json_encode(['success' => false, 'message' => 'Not signed in.']);
  exit;
}
// -----------------------------------------------------------------------

require_once __DIR__ . '/content_store.php';

$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body) || !isset($body['section'])) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Missing section.']);
  exit;
}

$section = $body['section'];
$data = isset($body['data']) ? $body['data'] : null;

if (!array_key_exists($section, CONTENT_DEFAULTS)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Unknown section: ' . $section]);
  exit;
}

$ok = content_save_section($section, $data);
if (!$ok) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Could not write content.json — check the php/data folder is writable.']);
  exit;
}

echo json_encode(['success' => true]);
