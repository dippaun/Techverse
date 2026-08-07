<?php
/**
 * Database connection — fill in your real Hostinger MySQL credentials below.
 * Find them in hPanel → Databases → your database → and the "Remote MySQL"
 * or "phpMyAdmin" section shows the host to use. On Hostinger shared hosting,
 * if this PHP code lives in the SAME hosting account as the database,
 * DB_HOST is almost always "localhost" — not the phpMyAdmin URL
 * (auth-db677.hstgr.io) and not 127.0.0.1:3306, those are internal addresses
 * phpMyAdmin itself uses.
 */

define('DB_HOST', 'localhost');                 // usually "localhost" on Hostinger
define('DB_NAME', 'u545632218_TechVerse');       // your database name
define('DB_USER', 'u545632218_admin');        // your database username
define('DB_PASS', 'Admin@techverse2026');      // your database password

function getDb() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
            exit;
        }
    }
    return $pdo;
}

/** Reads and JSON-decodes the request body for POST endpoints. */
function readJsonBody() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function jsonResponse($payload, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}
