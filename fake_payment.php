<?php
session_start();

$booking_id = $_GET['booking_id'] ?? '';
$car_id     = $_GET['car_id'] ?? '';
$user_id    = $_GET['user_id'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date   = $_GET['end_date'] ?? '';
$amount     = $_GET['amount'] ?? '';

// Fetch user bank details and PIN for realism
include 'partials/_dbconnect.php';
$u_sql = "SELECT bank_name, account_no, payment_pin FROM users WHERE id = ".intval($user_id);
$u_res = mysqli_query($conn, $u_sql);
$u_data = mysqli_fetch_assoc($u_res);

// Fetch Admin Details
$admin_sql = "SELECT id, bank_name, account_no FROM users WHERE role = 'admin' LIMIT 1";
$admin_res = mysqli_query($conn, $admin_sql);
$admin_data = mysqli_fetch_assoc($admin_res);

$correct_pin = $u_data['payment_pin'];
if (empty($correct_pin)) {
    echo "<script>alert('Please set your payment PIN in your profile before making a payment.'); window.location.href='profile.php';</script>";
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Secure Payment Gateway</title>
  <style>
    body { font-family:'Segoe UI',sans-serif; background:#f4f7f6; display:flex; justify-content:center; align-items:center; height:100vh; }
    .card { background:#fff; padding:30px; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.15); text-align:center; width:400px; }
    h2 { color:#2c7a7b; margin-bottom:20px; }
    .amount { font-size:22px; font-weight:bold; color:#e67e22; margin:15px 0; }
    .debug { text-align:left; font-size:14px; background:#f9f9f9; border:1px solid #ccc; padding:10px; margin-bottom:15px; }
    .numpad { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin:20px 0; }
    .numpad button { padding:15px; font-size:18px; border:none; border-radius:6px; background:#eee; cursor:pointer; }
    .numpad button:hover { background:#ddd; }
    .pay-btn { background:#28a745; color:#fff; padding:12px 20px; border:none; border-radius:6px; font-weight:bold; cursor:pointer; margin-top:20px; }
    .pay-btn:hover { background:#218838; }
    #pinDisplay { font-size:20px; margin-bottom:10px; border:1px solid #ccc; padding:10px; border-radius:6px; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Secure Payment Gateway</h2>
    <p class="amount">Amount: ₹<?= number_format($amount, 2) ?></p>

    <div style="background:#eef2ff; border:1px solid #c3dafe; padding:10px; border-radius:8px; margin-bottom:10px; text-align:left; font-size:14px;">
      <p style="margin:0 0 5px; color:#4338ca; font-weight:bold;">Paying from:</p>
      <strong><?= htmlspecialchars($u_data['bank_name'] ?? 'Unknown Bank') ?></strong><br>
      A/C: XXXX-XXXX-<?= substr($u_data['account_no'] ?? '0000', -4) ?>
    </div>

    <div style="background:#f0fff4; border:1px solid #c6f6d5; padding:10px; border-radius:8px; margin-bottom:20px; text-align:left; font-size:14px;">
      <p style="margin:0 0 5px; color:#22543d; font-weight:bold;">Transferring to Admin Account:</p>
      <strong><?= htmlspecialchars($admin_data['bank_name'] ?? 'Admin Bank') ?></strong><br>
      A/C: <?= htmlspecialchars($admin_data['account_no'] ?? 'N/A') ?>
    </div>

    <!-- Debug output
    <div class="debug">
      <strong>Debug Values:</strong><br>
      booking_id = <?= htmlspecialchars($booking_id) ?><br>
      car_id = <?= htmlspecialchars($car_id) ?><br>
      user_id = <?= htmlspecialchars($user_id) ?><br>
      start_date = <?= htmlspecialchars($start_date) ?><br>
      end_date = <?= htmlspecialchars($end_date) ?><br>
      amount = <?= htmlspecialchars($amount) ?><br>
    </div> -->

    <!-- Fake numpad -->
    <div id="pinDisplay">Enter PIN</div>
    <div class="numpad">
      <?php for($i=1;$i<=9;$i++): ?>
        <button type="button" onclick="addDigit('<?= $i ?>')"><?= $i ?></button>
      <?php endfor; ?>
      <button type="button" onclick="clearPin()">Clear</button>
      <button type="button" onclick="addDigit('0')">0</button>
      <button type="button" onclick="confirmPin()">OK</button>
    </div>

    <!-- Hidden forms -->
    <form id="successForm" method="post" action="process_payment.php" style="display:none;">
      <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
      <input type="hidden" name="car_id" value="<?= $car_id ?>">
      <input type="hidden" name="user_id" value="<?= $user_id ?>">
      <input type="hidden" name="start_date" value="<?= $start_date ?>">
      <input type="hidden" name="end_date" value="<?= $end_date ?>">
      <input type="hidden" name="amount" value="<?= $amount ?>">
      <input type="hidden" name="admin_id" value="<?= $admin_data['id'] ?? 1 ?>">
      <input type="hidden" name="status" value="success">
    </form>

    <form id="failForm" method="post" action="process_payment.php" style="display:none;">
      <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
      <input type="hidden" name="car_id" value="<?= $car_id ?>">
      <input type="hidden" name="user_id" value="<?= $user_id ?>">
      <input type="hidden" name="start_date" value="<?= $start_date ?>">
      <input type="hidden" name="end_date" value="<?= $end_date ?>">
      <input type="hidden" name="amount" value="<?= $amount ?>">
      <input type="hidden" name="admin_id" value="<?= $admin_data['id'] ?? 1 ?>">
      <input type="hidden" name="status" value="failed">
    </form>

    <button class="pay-btn" onclick="submitPayment()">Pay Now</button>
  </div>

  <script>
    let pin = "";
    let pinConfirmed = null;

    function addDigit(d) {
      pin += d;
      document.getElementById("pinDisplay").innerText = pin;
    }

    function clearPin() {
      pin = "";
      pinConfirmed = null;
      document.getElementById("pinDisplay").innerText = "Enter PIN";
    }

    function confirmPin() {
      if(pin === "<?= $correct_pin ?>") {
        alert("PIN Confirmed!");
        pinConfirmed = true;
      } else {
        alert("Incorrect PIN. Payment will fail.");
        pinConfirmed = false;
      }
    }

    function submitPayment() {
      if(pinConfirmed === true) {
        document.getElementById("successForm").submit();
      } else if(pinConfirmed === false) {
        document.getElementById("failForm").submit();
      } else {
        alert("Please enter and confirm PIN first.");
      }
    }
  </script>
</body>
</html>
