<?php
session_start();
include '../partials/_dbconnect.php';

// Only admins can archive bookings
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only.");
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Allow archiving only for cancelled, paid, or user-hidden bookings
    $check_sql = "SELECT status, user_hidden FROM bookings WHERE id=$id";
    $check_res = mysqli_query($conn, $check_sql);
    if ($check_res && mysqli_num_rows($check_res) > 0) {
        $row = mysqli_fetch_assoc($check_res);

        if ($row['user_hidden'] == 0 && !in_array($row['status'], ['cancelled', 'paid', 'user_deleted'])) {
            die("Only cancelled, paid, or user-deleted bookings can be marked as archived.");
        }
    } else {
        die("Booking not found.");
    }

    // Instead of deleting, mark as archived
    $sql_archive = "UPDATE bookings SET status='archived' WHERE id=$id";
    if (mysqli_query($conn, $sql_archive)) {
        header("Location: manage_bookings.php?msg=deleted");
        exit();
    } else {
        die("Error completing booking: " . mysqli_error($conn));
    }
} else {
    die("No booking ID provided.");
}
?>
