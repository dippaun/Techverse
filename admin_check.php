<?php
session_start();
require_once __DIR__ . '/config.php';
jsonResponse(['loggedIn' => !empty($_SESSION['admin_logged_in'])]);
