<?php
session_start();
include 'partials/_dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone     = mysqli_real_escape_string($conn, $_POST['phone']);
    $address   = mysqli_real_escape_string($conn, $_POST['address']);
    $license   = mysqli_real_escape_string($conn, $_POST['license_no']);
    $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
    $acc_no    = mysqli_real_escape_string($conn, $_POST['account_no']);
    $pin       = mysqli_real_escape_string($conn, $_POST['payment_pin']);

    $update_sql = "UPDATE users SET full_name='$full_name', phone='$phone', address='$address', license_no='$license', bank_name='$bank_name', account_no='$acc_no', payment_pin='$pin' WHERE id=$user_id";
    if (mysqli_query($conn, $update_sql)) {
        $msg = "<div class='alert alert-success'>✅ Profile updated successfully!</div>";
        // If redirected from booking, send them back
        if (isset($_GET['redirect']) && $_GET['redirect'] == 'book') {
            header("Location: index.php?msg=profile_done");
            exit();
        }
    } else {
        $msg = "<div class='alert alert-danger'>❌ Error updating profile: " . mysqli_error($conn) . "</div>";
    }
}

// Fetch current user data
$sql = "SELECT * FROM users WHERE id=$user_id";
$res = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($res);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile - Car Rental</title>
  <style>
    body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f0f2f5; margin:0; padding:0; }
    .profile-container { max-width:600px; margin:50px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.1); }
    h2 { color:#2c7a7b; text-align:center; margin-bottom:30px; }
    .form-group { margin-bottom:20px; }
    label { display:block; margin-bottom:8px; font-weight:600; color:#4a5568; }
    input[type="text"], input[type="tel"], textarea { width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px; box-sizing:border-box; font-size:16px; }
    textarea { height:100px; resize:vertical; }
    .btn-save { width:100%; padding:14px; background:#2c7a7b; color:#fff; border:none; border-radius:8px; font-size:18px; font-weight:bold; cursor:pointer; transition:background 0.3s; }
    .btn-save:hover { background:#285e5e; }
    .alert { padding:15px; border-radius:8px; margin-bottom:20px; text-align:center; }
    .alert-success { background:#c6f6d5; color:#22543d; }
    .alert-danger { background:#fed7d7; color:#822727; }
    .info-box { background:#ebf8ff; color:#2b6cb0; padding:15px; border-radius:8px; margin-bottom:20px; font-size:14px; border-left:4px solid #3182ce; }
  </style>
</head>
<body>
  <?php include 'partials/_nav.php'; ?>

  <div class="profile-container">
    <h2>My Profile Details</h2>
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'need_profile'): ?>
      <div class="info-box">ℹ️ Please complete your profile details before booking a car.</div>
    <?php endif; ?>

    <?= $msg ?>

    <form method="POST">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required placeholder="Enter your full name">
      </div>
      
      <div class="form-group">
        <label>Phone Number</label>
        <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required placeholder="Enter your mobile number">
      </div>

      <div class="form-group">
        <label>Driving License Number</label>
        <input type="text" name="license_no" value="<?= htmlspecialchars($user['license_no'] ?? '') ?>" required placeholder="e.g. DL-1234567890">
      </div>

      <div class="form-group">
        <label>Residential Address</label>
        <textarea name="address" required placeholder="Enter your full address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
      </div>

      <div style="margin:30px 0 20px; padding:15px; background:#f8fafc; border-radius:8px; border:1px dashed #cbd5e0;">
        <h3 style="margin:0 0 15px; font-size:16px; color:#2d3748;">🏦 Bank Details (For Payment)</h3>
        <div class="form-group">
          <label>Bank Name</label>
          <input type="text" name="bank_name" value="<?= htmlspecialchars($user['bank_name'] ?? '') ?>" required placeholder="e.g. State Bank of India">
        </div>
        <div class="form-group">
          <label>Account Number</label>
          <input type="text" name="account_no" value="<?= htmlspecialchars($user['account_no'] ?? '') ?>" required placeholder="Enter your 10-16 digit account number">
        </div>
        <div class="form-group">
          <label>Security PIN (for Payments)</label>
          <input type="password" name="payment_pin" value="<?= htmlspecialchars($user['payment_pin'] ?? '') ?>" required maxlength="6" placeholder="Enter a secure 4-6 digit PIN">
          <small style="color:#718096;">*Required for making payments</small>
        </div>
      </div>

      <button type="submit" class="btn-save">Save Profile Details</button>
    </form>

    <p style="text-align:center; margin-top:20px;">
      <a href="index.php" style="color:#2c7a7b; text-decoration:none;">⬅ Back to Home</a>
    </p>
  </div>
</body>
</html>
