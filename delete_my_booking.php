<?php
session_start();
include __DIR__ . '/partials/_dbconnect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    die("Access denied. This page is for users only.");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Verify booking belongs to this user
    $check_sql = "SELECT id FROM bookings WHERE id=$id AND user_id=$user_id";
    $check_res = mysqli_query($conn, $check_sql);

    if ($check_res && mysqli_num_rows($check_res) > 0) {
        // Mark booking as user_deleted
        $sql = "UPDATE bookings SET user_hidden=1 WHERE id=$id AND user_id=$user_id";
        if (mysqli_query($conn, $sql)) {
            header("Location: my_bookings.php?msg=deleted");
            exit();
        } else {
            die("Error deleting booking: " . mysqli_error($conn));
        }
    } else {
        die("Booking not found or not yours.");
    }
} else {
    die("No booking ID provided.");
}
?>
