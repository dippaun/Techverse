<?php
session_start();
require_once __DIR__ . '/config.php';

if (empty($_SESSION['admin_logged_in'])) {
    jsonResponse(['success' => false, 'message' => 'Not signed in.'], 401);
}
