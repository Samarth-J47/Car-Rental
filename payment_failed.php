<?php
session_start();
include 'partials/_dbconnect.php';

// Get details from URL to allow for a retry button
$car_id     = $_GET['car_id'] ?? '';
$user_id    = $_GET['user_id'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date   = $_GET['end_date'] ?? '';
$amount     = $_GET['amount'] ?? '';
$booking_id = $_GET['booking_id'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Failed | Car Rental</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; display: flex; align-items: center; justify-content: center; height: 100vh; }
    .container { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; max-width: 450px; width: 90%; }
    .icon { font-size: 60px; color: #e74c3c; margin-bottom: 20px; }
    h1 { color: #2c3e50; margin-bottom: 15px; font-size: 24px; }
    p { color: #7f8c8d; line-height: 1.6; margin-bottom: 30px; }
    .btn-group { display: flex; flex-direction: column; gap: 12px; }
    .btn { padding: 14px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 16px; transition: all 0.3s; cursor: pointer; border: none; }
    .btn-retry { background: #2c7a7b; color: white; }
    .btn-retry:hover { background: #225e5f; transform: translateY(-2px); }
    .btn-home { background: #6c757d; color: white; }
    .btn-home:hover { background: #5a6268; }
    .help-text { font-size: 13px; color: #95a5a6; margin-top: 25px; }
  </style>
</head>
<body>

  <div class="container">
    <h1>Payment Failed</h1>
    <p>We couldn't process your payment. This usually happens due to an incorrect PIN or a temporary issue with your bank.</p>

    <div class="btn-group">
      <?php if ($car_id): ?>
        <a href="fake_payment.php?booking_id=<?= $booking_id ?>&car_id=<?= $car_id ?>&user_id=<?= $user_id ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&amount=<?= $amount ?>" class="btn btn-retry">Try Payment Again</a>
      <?php endif; ?>
      <a href="index.php" class="btn btn-home">Back to Home</a>
    </div>

    <div class="help-text">
      Don't worry, no money was deducted from your account.
    </div>
  </div>

</body>
</html>
