<?php
include 'conn.php';

header('Content-Type: application/json');

if (!isset($_POST['token'])) {
    echo json_encode(['success' => false, 'message' => 'Token tidak dikirim']);
    exit;
}

$token = mysqli_real_escape_string($conn, $_POST['token']);
$q = mysqli_query($conn, "SELECT * FROM tokens WHERE token = '$token' LIMIT 1");

if (mysqli_num_rows($q) > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Token tidak valid']);
}
