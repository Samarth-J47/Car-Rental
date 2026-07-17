<?php
session_start();
include '../partials/_dbconnect.php';

// Access control: only admins allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only.");
}

// Handle admin delete request (soft delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_delete_id'])) {
    $delete_id = intval($_POST['admin_delete_id']);
    $sql_hide = "UPDATE payments SET admin_hidden = 1 WHERE id = $delete_id";
    mysqli_query($conn, $sql_hide);
    header("Location: admin_payments.php?msg=hidden");
    exit();
}

// Fetch all payments with user and car info
$sql = "SELECT p.id, p.booking_id, p.user_id, p.amount, p.status, p.created_at, p.user_hidden,
               u.username, c.car_name, c.brand
        FROM payments p
        JOIN users u ON p.user_id = u.id
        JOIN bookings b ON p.booking_id = b.id
        JOIN cars c ON b.car_id = c.id
        WHERE p.admin_hidden = 0
        ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Payment Records</title>
  <style>
    body { font-family:Arial,sans-serif; background:#f4f4f9; margin:0; padding:0; }
    .container { max-width:1100px; margin:40px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
    h2 { text-align:center; color:#2c7a7b; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th, td { padding:12px; border:1px solid #ddd; text-align:center; }
    th { background:#333; color:#fff; }
    tr:nth-child(even) { background:#f9f9f9; }
    .badge { padding:5px 10px; border-radius:4px; font-size:13px; font-weight:bold; color:#fff; }
    .bg-success { background:#28a745; }
    .bg-danger  { background:#dc3545; }
    .bg-warning { background:#ffc107; }
  </style>
</head>
<body>
  <?php require '../partials/_nav.php'; ?>

  <div class="container">
    <h2>All Payment Records</h2>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'hidden'): ?>
      <div class="alert alert-success" style="background:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:20px; text-align:center;">✅ Payment record hidden from your view!</div>
    <?php endif; ?>
    <?php if (mysqli_num_rows($result) > 0) { ?>
      <table>
        <thead>
          <tr>
            <th>Serial No.</th>
            <th>Payment ID</th>
            <th>User</th>
            <th>Car</th>
            <th>Brand</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $serial = 1; // start serial number
          while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><?= $serial++ ?></td>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['car_name']) ?></td>
              <td><?= htmlspecialchars($row['brand']) ?></td>
              <td>₹<?= number_format($row['amount'], 2) ?></td>
              <td>
                <?php if ($row['status'] == 'success'): ?>
                  <span class="badge bg-success">Success</span>
                <?php elseif ($row['status'] == 'failed'): ?>
                  <span class="badge bg-danger">Failed</span>
                <?php else: ?>
                  <span class="badge bg-warning"><?= ucfirst($row['status']) ?></span>
                <?php endif; ?>
              </td>
              <td><?= $row['created_at'] ?></td>
              <td>
                <form method="post" style="display:inline;" onsubmit="return confirm('Remove this payment from your view? (It will still be visible to the user)');">
                  <input type="hidden" name="admin_delete_id" value="<?= $row['id'] ?>">
                  <button type="submit" class="badge bg-danger" style="border:none; cursor:pointer;">Delete</button>
                </form>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>No payments found.</p>
    <?php } ?>

    <!-- Back button -->
    <button onclick="window.location.href='dashboard.php'" 
            style="margin-top:20px; background:#6c757d; color:#fff; padding:10px 18px; border:none; border-radius:5px; cursor:pointer;">
      ⬅ Back to Dashboard
    </button>
  </div>
</body>
</html>
