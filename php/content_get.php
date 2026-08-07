<?php
/**
 * content_get.php
 * Public read endpoint — no login required, since competitions.html,
 * sponsors.html, workshops.html and contact.html all call this directly
 * to render their content. Returns the full content object, or a single
 * section if ?section=xxx is passed.
 */

header('Content-Type: application/json');
// Loosen this if the public pages are ever served from a different origin
// than this php/ folder (e.g. a separate static host):
// header('Access-Control-Allow-Origin: https://your-site.example.com');

require_once __DIR__ . '/content_store.php';

$all = content_load();

$section = isset($_GET['section']) ? $_GET['section'] : null;
if ($section) {
  if (!array_key_exists($section, $all)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Unknown section.']);
    exit;
  }
  echo json_encode(['success' => true, 'content' => [$section => $all[$section]]]);
  exit;
}

echo json_encode(['success' => true, 'content' => $all]);
