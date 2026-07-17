<?php
session_start();
include __DIR__ . '/partials/_dbconnect.php';

// Security check: Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$booking_id = intval($_GET['booking_id'] ?? 0);

if ($booking_id <= 0) {
    // If no booking_id, redirect back to index.php
    header("Location: index.php");
    exit();
}

// Fetch booking, car, user, and successful payment details
$sql = "SELECT b.id AS booking_id, b.start_date, b.end_date, b.total_price, b.status AS booking_status,
               c.car_name, c.brand, c.seats, c.price_per_day,
               p.id AS payment_id, p.created_at AS payment_date, p.amount AS paid_amount, p.status AS payment_status,
               u.full_name, u.username, u.email, u.phone, u.license_no, u.address, u.bank_name, u.account_no
        FROM bookings b
        JOIN cars c ON b.car_id = c.id
        JOIN users u ON b.user_id = u.id
        LEFT JOIN payments p ON p.booking_id = b.id AND p.status = 'success'
        WHERE b.id = $booking_id " . ($is_admin ? "" : "AND b.user_id = $user_id") . "
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    die("<p style='color:red;text-align:center;margin-top:50px;'>Error: Booking details not found or access denied.</p>
         <p style='text-align:center;'><a href='index.php'>Go back to Home</a></p>");
}

$data = mysqli_fetch_assoc($result);

$bookingIdPad = str_pad($data['booking_id'], 5, '0', STR_PAD_LEFT);
$paymentIdPad = $data['payment_id'] ? str_pad($data['payment_id'], 5, '0', STR_PAD_LEFT) : 'N/A';
$fullName = !empty($data['full_name']) ? $data['full_name'] : $data['username'];
$phone = !empty($data['phone']) ? $data['phone'] : 'N/A';
$license = !empty($data['license_no']) ? $data['license_no'] : 'N/A';
$address = !empty($data['address']) ? $data['address'] : 'N/A';
$bankName = !empty($data['bank_name']) ? $data['bank_name'] : 'N/A';
$accNoLast4 = !empty($data['account_no']) ? 'XXXX-XXXX-' . substr($data['account_no'], -4) : 'N/A';
$paymentDate = $data['payment_date'] ? date('M d, Y H:i A', strtotime($data['payment_date'])) : 'Pending';

$days = (strtotime($data['end_date']) - strtotime($data['start_date'])) / (60*60*24) + 1;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Successful - Car Rental</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Include Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2c7a7b;
      --primary-dark: #1f5758;
      --secondary: #e67e22;
      --success: #28a745;
      --success-bg: #e6f7ed;
      --bg: #f3f6f9;
      --card-bg: #ffffff;
      --text: #2d3748;
      --text-muted: #718096;
      --border: #e2e8f0;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Outfit', sans-serif;
      background-color: var(--bg);
      color: var(--text);
      line-height: 1.6;
      padding: 40px 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .container {
      width: 100%;
      max-width: 700px;
    }

    /* Print-specific overrides */
    @media print {
      body {
        background: #ffffff;
        color: #000000;
        padding: 0;
        margin: 0;
      }
      .no-print {
        display: none !important;
      }
      .card {
        box-shadow: none !important;
        border: 1px solid #000 !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      .receipt-box {
        border: none !important;
        background: none !important;
      }
      .section-title {
        border-bottom: 1px solid #000 !important;
      }
    }

    .card {
      background: var(--card-bg);
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(44, 122, 123, 0.08), 0 5px 15px rgba(0, 0, 0, 0.04);
      padding: 40px;
      text-align: center;
      animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
      overflow: hidden;
    }

    /* Top decorative color strip */
    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 8px;
      background: linear-gradient(90deg, var(--primary), var(--success));
    }

    .success-icon-wrap {
      display: inline-flex;
      justify-content: center;
      align-items: center;
      width: 80px;
      height: 80px;
      background-color: var(--success-bg);
      border-radius: 50%;
      margin-bottom: 24px;
      animation: scaleUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
      color: var(--success);
      font-size: 40px;
    }

    h1 {
      font-size: 32px;
      font-weight: 700;
      color: var(--success);
      margin-bottom: 10px;
      letter-spacing: -0.5px;
    }

    .subtitle {
      color: var(--text-muted);
      font-size: 16px;
      margin-bottom: 30px;
    }

    /* Receipt Box Styling */
    .receipt-box {
      text-align: left;
      background: #f8fafc;
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 24px;
      margin-bottom: 30px;
    }

    .receipt-header {
      display: flex;
      justify-content: space-between;
      border-bottom: 2px dashed var(--border);
      padding-bottom: 15px;
      margin-bottom: 20px;
    }

    .receipt-header-item {
      font-size: 14px;
    }

    .receipt-header-item strong {
      display: block;
      font-size: 16px;
      color: var(--text);
      margin-top: 4px;
    }

    .receipt-header-item.right {
      text-align: right;
    }

    .section-title {
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--primary);
      font-weight: 700;
      margin-bottom: 12px;
      border-bottom: 1px solid var(--border);
      padding-bottom: 4px;
    }

    .receipt-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
      margin-bottom: 24px;
    }

    .receipt-grid-full {
      grid-column: span 2;
    }

    .info-label {
      font-size: 13px;
      color: var(--text-muted);
      margin-bottom: 2px;
    }

    .info-val {
      font-size: 15px;
      font-weight: 600;
      color: var(--text);
    }

    .divider {
      height: 1px;
      background-color: var(--border);
      margin: 20px 0;
    }

    /* Total Paid Panel */
    .total-panel {
      background-color: #edf2f7;
      border-radius: 10px;
      padding: 16px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 15px;
    }

    .total-title {
      font-size: 16px;
      font-weight: 600;
      color: var(--text);
    }

    .total-price {
      font-size: 24px;
      font-weight: 700;
      color: var(--primary);
    }

    /* Buttons Actions */
    .btn-group {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 12px;
      margin-top: 30px;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 14px 24px;
      border-radius: 10px;
      font-weight: 600;
      text-decoration: none;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.2s ease-in-out;
      border: none;
      outline: none;
    }

    .btn-primary {
      background-color: var(--primary);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(44, 122, 123, 0.2);
    }

    .btn-primary:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
    }

    .btn-outline {
      background-color: transparent;
      border: 2px solid var(--primary);
      color: var(--primary);
    }

    .btn-outline:hover {
      background-color: rgba(44, 122, 123, 0.05);
      transform: translateY(-2px);
    }

    .btn-secondary {
      background-color: #edf2f7;
      color: #4a5568;
    }

    .btn-secondary:hover {
      background-color: #e2e8f0;
      transform: translateY(-2px);
    }

    /* Keyframes */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes scaleUp {
      from {
        transform: scale(0.6);
        opacity: 0;
      }
      to {
        transform: scale(1);
        opacity: 1;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="card">
      <div class="success-icon-wrap">
        ✓
      </div>
      <h1>Booking & Payment Successful!</h1>
      <p class="subtitle">Thank you for your payment. Your reservation has been finalized.</p>

      <!-- Receipt Preview -->
      <div class="receipt-box">
        <div class="receipt-header">
          <div class="receipt-header-item">
            Booking ID
            <strong>#BK-<?= $bookingIdPad ?></strong>
          </div>
          <div class="receipt-header-item right">
            Receipt ID
            <strong>#PAY-<?= $paymentIdPad ?></strong>
          </div>
        </div>

        <!-- Customer Section -->
        <div class="section-title">Customer Information</div>
        <div class="receipt-grid">
          <div>
            <div class="info-label">Full Name</div>
            <div class="info-val"><?= htmlspecialchars($fullName) ?></div>
          </div>
          <div>
            <div class="info-label">Email Address</div>
            <div class="info-val"><?= htmlspecialchars($data['email']) ?></div>
          </div>
          <div>
            <div class="info-label">Phone Number</div>
            <div class="info-val"><?= htmlspecialchars($phone) ?></div>
          </div>
          <div>
            <div class="info-label">Driver's License</div>
            <div class="info-val"><?= htmlspecialchars($license) ?></div>
          </div>
          <div class="receipt-grid-full">
            <div class="info-label">Address</div>
            <div class="info-val"><?= htmlspecialchars($address) ?></div>
          </div>
        </div>

        <!-- Vehicle Section -->
        <div class="section-title">Vehicle Specifications</div>
        <div class="receipt-grid">
          <div>
            <div class="info-label">Car Model</div>
            <div class="info-val"><?= htmlspecialchars($data['car_name']) ?></div>
          </div>
          <div>
            <div class="info-label">Brand / Make</div>
            <div class="info-val"><?= htmlspecialchars($data['brand']) ?></div>
          </div>
          <div>
            <div class="info-label">Seating Capacity</div>
            <div class="info-val"><?= htmlspecialchars($data['seats']) ?> Seats</div>
          </div>
          <div>
            <div class="info-label">Rate</div>
            <div class="info-val">₹<?= number_format($data['price_per_day'], 2) ?> / day</div>
          </div>
        </div>

        <!-- Rental Period Section -->
        <div class="section-title">Rental & Payment Info</div>
        <div class="receipt-grid">
          <div>
            <div class="info-label">Start Date</div>
            <div class="info-val"><?= date('M d, Y', strtotime($data['start_date'])) ?></div>
          </div>
          <div>
            <div class="info-label">End Date</div>
            <div class="info-val"><?= date('M d, Y', strtotime($data['end_date'])) ?></div>
          </div>
          <div>
            <div class="info-label">Total Duration</div>
            <div class="info-val"><?= $days ?> Day(s)</div>
          </div>
          <div>
            <div class="info-label">Paid Via</div>
            <div class="info-val"><?= htmlspecialchars($bankName) ?> (<?= $accNoLast4 ?>)</div>
          </div>
          <div class="receipt-grid-full">
            <div class="info-label">Payment Date</div>
            <div class="info-val"><?= $paymentDate ?></div>
          </div>
        </div>

        <div class="total-panel">
          <div class="total-title">Total Amount Paid</div>
          <div class="total-price">₹<?= number_format($data['total_price'], 2) ?></div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="btn-group no-print">
        <!-- Download Receipt TXT -->
        <a href="download_receipt.php?booking_id=<?= $data['booking_id'] ?>" class="btn btn-primary">
          📥 Download Receipt (.txt)
        </a>
        
        <!-- Print Receipt / Save as PDF -->
        <button onclick="window.print()" class="btn btn-outline">
          🖨️ Print / Save PDF
        </button>

        <!-- View Bookings -->
        <a href="my_bookings.php" class="btn btn-secondary">
          My Bookings
        </a>

        <!-- Back to Home -->
        <a href="index.php" class="btn btn-secondary">
          ⬅ Back to Cars
        </a>
      </div>
    </div>
  </div>

</body>
</html>
