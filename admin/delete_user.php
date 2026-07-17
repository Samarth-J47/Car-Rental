<?php
session_start();
include '../partials/_dbconnect.php';

// Access control: only admins allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only.");
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Check if the user is an admin
    $checkRole = mysqli_query($conn, "SELECT role FROM users WHERE id=$id");
    $userData = mysqli_fetch_assoc($checkRole);

    if ($userData && $userData['role'] === 'admin') {
        // Block deletion of admin accounts
        header("Location: manage_users.php?msg=cannot_delete_admin");
        exit();
    }

    // Check if user has bookings
    $checkBookings = mysqli_query($conn, "SELECT * FROM bookings WHERE user_id=$id");
    if (mysqli_num_rows($checkBookings) > 0) {
        // Block deletion if user has bookings
        header("Location: manage_users.php?msg=has_bookings");
        exit();
    } else {
        // Safe to delete
        $sql = "DELETE FROM users WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            header("Location: manage_users.php?msg=deleted");
            exit();
        } else {
            die("Error deleting user: " . mysqli_error($conn));
        }
    }
} else {
    die("No user ID provided.");
}
?>
