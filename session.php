<?php
session_start();
require_once __DIR__ . '/db.php';

/* ===============================
   FORCE LOGOUT IF INACTIVE
================================ */

if (isset($_SESSION['user_id'])) {

    $conn = getDB();

    $stmt = $conn->prepare("
        SELECT is_active 
        FROM users 
        WHERE emp_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // ❌ User removed or inactive → logout
    if (!$user || (int)$user['is_active'] === 0) {
        session_unset();
        session_destroy();

        header("Location: /login.php?inactive=1");
        exit;
    }
}
