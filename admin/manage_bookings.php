<?php
session_start();
include '../partials/_dbconnect.php'; 

// Access control: only admins allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only.");
}

$sql = "SELECT b.id AS booking_id, b.start_date, b.end_date, b.total_price, b.status,
               u.username, c.car_name, c.brand, b.user_hidden
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN cars c ON b.car_id = c.id
        WHERE b.status IS NOT NULL 
          AND b.status <> '' 
          AND b.status <> 'archived'
        ORDER BY b.start_date DESC";

$result = mysqli_query($conn, $sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Bookings</title>
  <style>
    body { background:#f4f4f9; font-family:Arial,sans-serif; margin:0; padding:0; }
    .container { max-width:1100px; margin:40px auto; padding:0 20px; }
    h2 { text-align:center; margin-bottom:20px; color:#2c7a7b; }
    .alert { padding:12px; border-radius:5px; margin-bottom:20px; text-align:center; font-weight:bold; }
    .alert-success { background:#d4edda; color:#155724; }
    .alert-warning { background:#fff3cd; color:#856404; }
    .alert-danger  { background:#f8d7da; color:#721c24; }
    table { width:100%; border-collapse:collapse; margin-top:20px; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
    th, td { padding:12px; border:1px solid #ddd; text-align:center; }
    th { background:#333; color:#fff; }
    .badge { padding:5px 10px; border-radius:4px; font-size:13px; font-weight:bold; color:#fff; }
    .bg-success { background:#28a745; }
    .bg-danger  { background:#dc3545; }
    .bg-warning { background:#ffc107; }
    .btn { display:inline-block; padding:6px 12px; border-radius:4px; text-decoration:none; font-size:14px; font-weight:bold; cursor:pointer; }
    .btn-success { background:#28a745; color:#fff; }
    .btn-success:hover { background:#218838; }
    .btn-danger { background:#dc3545; color:#fff; }
    .btn-danger:hover { background:#b52a37; }
  </style>
</head>
<body>
  <?php require '../partials/_nav.php'; ?>

  <div class="container">
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'confirmed'): ?>
      <div class="alert alert-success">✅ Booking confirmed successfully!</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'cancelled'): ?>
      <div class="alert alert-danger">❌ Booking cancelled!</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
      <div class="alert alert-success">🗑️ Booking deleted permanently!</div>
    <?php endif; ?>

    <h2>Manage All Bookings</h2>

    <?php if (mysqli_num_rows($result) > 0) { ?>
      <table>
        <thead>
          <tr>
            <th>User</th>
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
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['car_name']) ?></td>
              <td><?= htmlspecialchars($row['brand']) ?></td>
              <td><?= htmlspecialchars($row['start_date']) ?></td>
              <td><?= htmlspecialchars($row['end_date']) ?></td>
              <td>₹<?= $row['total_price'] ?></td>
              <td>
                <?php if ($row['status'] == 'pending'): ?>
                  <span class="badge bg-warning">Pending</span>
                <?php elseif ($row['status'] == 'confirmed'): ?>
                  <span class="badge bg-success">Confirmed</span>
                <?php elseif ($row['status'] == 'paid'): ?>
                  <span class="badge bg-success">Paid</span>
                <?php elseif ($row['status'] == 'cancelled'): ?>
                  <span class="badge bg-danger">Cancelled</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($row['status'] == 'pending'): ?>
                  <a href="update_booking.php?action=confirm&id=<?= $row['booking_id'] ?>" class="btn btn-success">Confirm</a>
                  <a href="update_booking.php?action=cancel&id=<?= $row['booking_id'] ?>" class="btn btn-danger"
                     onclick="return confirm('Cancel this booking request?');">Cancel</a>
                <?php elseif ($row['status'] == 'confirmed'): ?>
                  <a href="update_booking.php?action=cancel&id=<?= $row['booking_id'] ?>" class="btn btn-danger"
                     onclick="return confirm('Cancel this confirmed booking?');">Cancel</a>
                <?php elseif ($row['status'] == 'paid' || $row['status'] == 'cancelled'): ?>
                  <a href="delete_booking.php?id=<?= $row['booking_id'] ?>" class="btn btn-danger"
                     onclick="return confirm('Delete this <?= $row['status'] ?> booking permanently?');">Delete</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <div class="alert alert-warning">No bookings found.</div>
    <?php } ?>

    <button onclick="window.location.href='dashboard.php'" 
            style="margin-top:20px; background:#6c757d; color:#fff; padding:10px 18px; border:none; border-radius:5px; cursor:pointer;">
      ⬅ Back to Dashboard
    </button>
  </div>
</body>
</html>
