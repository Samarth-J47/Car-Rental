<?php
session_start();
include __DIR__ . '/partials/_dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    die("<p style='color:red;text-align:center;'>You must be logged in to view payment history.</p>
         <p style='text-align:center;'><a href='index.php'>Back to Cars</a></p>");
}

$user_id = $_SESSION['user_id'];

// Handle delete request (independent of bookings)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $del_sql = "UPDATE payments SET user_hidden=1 WHERE id=$delete_id AND user_id=$user_id";
    mysqli_query($conn, $del_sql);
    header("Location: payment_history.php"); // refresh after delete
    exit();
}

$sql = "SELECT p.id, p.amount, p.status, p.created_at, b.car_id, b.start_date, b.end_date 
        FROM payments p 
        LEFT JOIN bookings b ON p.booking_id = b.id 
        WHERE p.user_id = $user_id AND p.user_hidden = 0
        ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment History</title>
  <style>
    body { font-family:'Segoe UI',sans-serif; background:#f4f7f6; padding:20px; }
    table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
    th, td { padding:12px; border:1px solid #ddd; text-align:center; }
    th { background:#2c7a7b; color:#fff; }
    tr:nth-child(even) { background:#f9f9f9; }
    h2 { text-align:center; margin-bottom:20px; color:#2c7a7b; }
    .delete-btn { background:#dc3545; color:#fff; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; }
    .delete-btn:hover { background:#b52a37; }
    .back-btn { display:inline-block; margin-bottom:20px; background:#007bff; color:#fff; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:bold; }
    .back-btn:hover { background:#0056b3; }
  </style>
</head>
<body>
  <a href="index.php" class="back-btn">⬅ Back to Cars</a>
  <h2>My Payment History</h2>
  <table>
    <tr>
      <th>Serial</th>
      <th>Payment ID</th>
      <th>Amount</th>
      <th>Status</th>
      <th>Date</th>
      <th>Car ID</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th>Action</th>
    </tr>
    <?php 
    $serial = 1;
    while($row = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?= $serial++ ?></td>
        <td><?= $row['id'] ?></td>
        <td>₹<?= number_format($row['amount'], 2) ?></td>
        <td style="color:<?= $row['status']=='success'?'green':'red' ?>;">
          <?= ucfirst($row['status']) ?>
        </td>
        <td><?= $row['created_at'] ?></td>
        <td><?= $row['car_id'] ?></td>
        <td><?= $row['start_date'] ?></td>
        <td><?= $row['end_date'] ?></td>
        <td>
          <form method="post">
            <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
            <button type="submit" class="delete-btn">Delete</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
