<?php
require_once './../session.php';
require_once './../db.php';

header('Content-Type: application/json');

$conn = getDB();

/* ===============================
   AUTH CHECK
================================ */
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'owner')) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit;
}

/* ===============================
   GET POST DATA
================================ */
$data = json_decode(file_get_contents('php://input'), true);

$payment_id    = $data['payment_id'] ?? null;
$attendance_id = $data['attendance_id'] ?? null;
$cash_payment  = $data['cash_payment'] ?? 0;
$ni_payment    = $data['ni_payment'] ?? 0;
$payment_status= $data['payment_status'] ?? 'pending';

if(!$attendance_id){
    echo json_encode(['success'=>false,'message'=>'Attendance ID missing']);
    exit;
}

/* ===============================
   CHECK IF PAYMENT EXISTS
================================ */
if($payment_id){
    // Update existing payment
    $stmt = $conn->prepare("UPDATE payment_track 
                            SET cash_payment=?, ni_payment=?, payment_status=?, updated_at=NOW()
                            WHERE payment_id=?");
    $stmt->bind_param("ddsi", $cash_payment, $ni_payment, $payment_status, $payment_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success'=>true,'message'=>'Payment updated successfully']);
} else {
    // Insert new payment
    $stmt = $conn->prepare("INSERT INTO payment_track
                            (attendance_id, cash_payment, ni_payment, payment_status, created_at, updated_at)
                            VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("idds", $attendance_id, $cash_payment, $ni_payment, $payment_status);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success'=>true,'message'=>'Payment created successfully']);
}
