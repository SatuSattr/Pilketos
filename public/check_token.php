<?php
include 'conn.php';

header('Content-Type: application/json');

// Pastikan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
    exit;
}

// Cek apakah token dikirim
if (!isset($_POST['token']) || trim($_POST['token']) === '') {
    echo json_encode(['success' => false, 'message' => 'Token tidak dikirim']);
    exit;
}

// Sanitasi input dasar
$token = trim($_POST['token']);

// Gunakan prepared statement untuk menghindari SQL Injection
$stmt = $conn->prepare("SELECT id FROM tokens WHERE token = ? AND active = 1 LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Token tidak valid']);
}

$stmt->close();
$conn->close();
