<?php
session_start();
include 'partials/_dbconnect.php';

if (!isset($_GET['booking_id'])) {
    die("Booking not selected.");
}
$booking_id = (int)$_GET['booking_id'];

$sql = "SELECT * FROM bookings WHERE id='$booking_id'";
$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Booking not found.");
}
$booking = mysqli_fetch_assoc($result);

if ($booking['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'admin') {
    die("Unauthorized action.");
}

mysqli_query($conn, "UPDATE bookings SET status='cancelled' WHERE id='$booking_id'");
$car_id = $booking['car_id'];
mysqli_query($conn, "UPDATE cars SET status='available' WHERE id='$car_id'");

$message = "Booking has been cancelled successfully!";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Cancelled</title>
  <style>
    body {
      background-color: #f4f4f9;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      margin: 80px auto;
      padding: 25px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    h2 {
      color: #dc3545;
      margin-bottom: 20px;
    }
    .alert {
      padding: 15px;
      border-radius: 5px;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .alert.success {
      background: #d4edda;
      color: #155724;
    }
    a.btn {
      display: inline-block;
      padding: 10px 18px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      background: #2c7a7b;
      color: #fff;
      transition: background 0.3s;
    }
    a.btn:hover {
      background: #225e5f;
    }
  </style>
</head>
<body>
  <?php require 'partials/_nav.php'; ?>

  <div class="container">
  <h2>Booking Cancelled</h2>
  <div class="alert success"><?= $message; ?></div>
  <?php if ($_SESSION['role'] === 'admin') { ?>
    <a href="admin/manage_bookings.php?msg=cancelled" class="btn">Back to Manage Bookings</a>
  <?php } else { ?>
    <a href="my_bookings.php?msg=cancelled" class="btn">Back to My Bookings</a>
  <?php } ?>

  <!-- Back button to previous page -->
  <button onclick="history.back()" class="btn" style="margin-top:15px; background:#6c757d;">
    ⬅ Back
  </button>
</div>

</body>
</html>