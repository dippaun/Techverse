<?php

$host = "localhost";
$dbname = "u545632218_TechVerse";
$username = "u545632218_admin";
$password = "Admin@techverse2026";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>