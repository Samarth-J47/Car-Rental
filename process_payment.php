<?php
session_start();
include __DIR__ . '/partials/_dbconnect.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

// Collect POST data safely
$booking_id = intval($_POST['booking_id'] ?? 0);
$user_id    = intval($_POST['user_id'] ?? 0);
$admin_id   = intval($_POST['admin_id'] ?? 0);
$car_id     = intval($_POST['car_id'] ?? 0);
$start_date = $_POST['start_date'] ?? '';
$end_date   = $_POST['end_date'] ?? '';
$amount     = floatval($_POST['amount'] ?? 0);
$status     = $_POST['status'] ?? 'failed';

// Record payment attempt
$sql_payment = "INSERT INTO payments (user_id, admin_id, booking_id, amount, status, created_at) 
                VALUES ($user_id, $admin_id, $booking_id, $amount, '$status', NOW())";

if (!mysqli_query($conn, $sql_payment)) {
    die("Error recording payment: " . mysqli_error($conn));
}

if ($status === "success") {
    // Update booking to 'paid'
    $sql_update = "UPDATE bookings SET status='paid' WHERE id=$booking_id";
    if (!mysqli_query($conn, $sql_update)) {
        die("Error updating booking: " . mysqli_error($conn));
    }

    if (mysqli_affected_rows($conn) === 0) {
        die("No booking row was updated. booking_id=$booking_id may be invalid or status already 'paid'.");
    }

    header("Location: booking_success.php?booking_id=$booking_id");
    exit();
} else {
    header("Location: my_bookings.php?msg=failed");
    exit();
}
?>
