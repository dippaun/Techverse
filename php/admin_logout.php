<?php
session_start();
require_once __DIR__ . '/config.php';
$_SESSION = [];
session_destroy();
jsonResponse(['success' => true]);
