<?php
session_start();
include '../partials/_dbconnect.php';

// Access control: only admins allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only.");
}

// Validate input
$action = $_GET['action'] ?? '';
$booking_id = intval($_GET['id'] ?? 0);

if ($booking_id <= 0 || !in_array($action, ['confirm','cancel','mark_paid'])) {
    die("Invalid request.");
}

// Decide new status
$new_status = '';
$msg = '';

switch ($action) {
    case 'confirm':
        $new_status = 'confirmed';
        $msg = 'confirmed';
        break;
    case 'cancel':
        $new_status = 'cancelled';
        $msg = 'cancelled';
        break;
    case 'mark_paid':
        $new_status = 'paid';
        $msg = 'paid';
        break;
}

// Update booking
$sql = "UPDATE bookings SET status='$new_status' WHERE id=$booking_id";
if (!mysqli_query($conn, $sql)) {
    die("Error updating booking: " . mysqli_error($conn));
}

if (mysqli_affected_rows($conn) === 0) {
    die("No booking row was updated. ID may be invalid or already set to '$new_status'.");
}

// Redirect back with message
header("Location: manage_bookings.php?msg=$msg");
exit();
?>
