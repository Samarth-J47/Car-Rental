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

    // Mark booking as cancelled
    $sql = "UPDATE bookings SET status='cancelled' WHERE id=$id AND user_id=$user_id";
    if (mysqli_query($conn, $sql)) {
        header("Location: my_bookings.php?msg=deleted");
        exit();
    } else {
        die("Error cancelling booking: " . mysqli_error($conn));
    }
} else {
    die("No booking ID provided.");
}
?>
