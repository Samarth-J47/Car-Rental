<?php
session_start();
include 'partials/_dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your bookings.");
}
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    die("Access denied. This page is for users only.");
}

$user_id = $_SESSION['user_id'];

// Show all bookings except user_deleted
$sql = "SELECT b.id AS booking_id, b.start_date, b.end_date, b.total_price, b.status,
               b.car_id, c.car_name, c.brand
        FROM bookings b
        JOIN cars c ON b.car_id = c.id
        WHERE b.user_id = '$user_id'
          AND b.user_hidden = 0
        ORDER BY b.start_date DESC";

$result = mysqli_query($conn, $sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Bookings</title>
  <style>
    body { background-color:#f4f4f9; font-family:Arial,sans-serif; margin:0; padding:0; }
    .container { max-width:1000px; margin:40px auto; padding:20px; background:#fff; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
    h2 { text-align:center; margin-bottom:20px; color:#2c7a7b; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th, td { padding:12px; border:1px solid #ddd; text-align:center; }
    th { background:#333; color:#fff; }
    tr:nth-child(even) { background:#f9f9f9; }
    .alert { padding:12px; border-radius:5px; margin-bottom:20px; text-align:center; font-weight:bold; }
    .alert-success { background:#d4edda; color:#155724; }
    .alert-warning { background:#fff3cd; color:#856404; }
    .btn { display:inline-block; padding:6px 12px; border-radius:4px; text-decoration:none; font-size:14px; font-weight:bold; cursor:pointer; }
    .btn-danger { background:#dc3545; color:#fff; }
    .btn-danger:hover { background:#b52a37; }
    .btn-success { background:#28a745; color:#fff; }
    .btn-success:hover { background:#218838; }
    .text-muted { color:#6c757d; }
  </style>
</head>
<body>
  <?php require 'partials/_nav.php'; ?>

  <div class="container">
    <h2>My Bookings</h2>

    <!-- Alerts -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
      <div class="alert alert-success">✅ Booking removed from My Bookings!</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'paid'): ?>
      <div class="alert alert-success">✅ Payment completed successfully!</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'failed'): ?>
      <div class="alert alert-warning">⚠️ Payment failed. Please try again.</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'confirmed'): ?>
      <div class="alert alert-success">✅ Admin confirmed your booking, please proceed to payment!</div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) > 0) { ?>
      <table>
        <thead>
          <tr>
            <th>Car</th>
            <th>Brand</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><?= htmlspecialchars($row['car_name']) ?></td>
              <td><?= htmlspecialchars($row['brand']) ?></td>
              <td><?= htmlspecialchars($row['start_date']) ?></td>
              <td><?= htmlspecialchars($row['end_date']) ?></td>
              <td>₹<?= $row['total_price'] ?></td>
              <td><?= ucfirst($row['status']) ?></td>
              <td style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                <?php if ($row['status'] == 'confirmed'): ?>
                  <a href="fake_payment.php?booking_id=<?= $row['booking_id'] ?>&car_id=<?= $row['car_id'] ?>&user_id=<?= $_SESSION['user_id'] ?>&start_date=<?= $row['start_date'] ?>&end_date=<?= $row['end_date'] ?>&amount=<?= $row['total_price'] ?>" 
                     class="btn btn-success">Pay Now</a>
                  <a href="cancel_booking.php?id=<?= $row['booking_id'] ?>" 
                     class="btn btn-danger" 
                     onclick="return confirm('Cancel this booking?');">Cancel</a>

                <?php elseif ($row['status'] == 'pending'): ?>
                  <a href="cancel_booking.php?id=<?= $row['booking_id'] ?>" 
                     class="btn btn-danger" 
                     onclick="return confirm('Cancel this booking?');">Cancel</a>
                <?php endif; ?>

                <!-- Always show Delete (Remove) button -->
                <a href="delete_my_booking.php?id=<?= $row['booking_id'] ?>" 
                   class="btn btn-danger" 
                   style="background:#6c757d;"
                   onclick="return confirm('Remove this booking from your view?');">Remove</a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <div class="alert alert-warning">You have no bookings yet.</div>
    <?php } ?>

    <!-- Back button -->
    <button onclick="window.location.href='index.php'" 
            style="margin-top:20px; background:#6c757d; color:#fff; padding:10px 18px; border:none; border-radius:5px; cursor:pointer;">
      ⬅ Back to Home
    </button>
  </div>
</body>
</html>
