<?php
session_start();
include __DIR__ . '/partials/_dbconnect.php';

// Security check: Must be logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to download receipts.");
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$booking_id = intval($_GET['booking_id'] ?? 0);

if ($booking_id <= 0) {
    die("Error: Invalid booking ID.");
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
    die("Error: Booking not found or access denied.");
}

$data = mysqli_fetch_assoc($result);

// Format details
$bookingIdPad = str_pad($data['booking_id'], 5, '0', STR_PAD_LEFT);
$paymentIdPad = $data['payment_id'] ? str_pad($data['payment_id'], 5, '0', STR_PAD_LEFT) : 'N/A';
$fullName = !empty($data['full_name']) ? $data['full_name'] : $data['username'];
$phone = !empty($data['phone']) ? $data['phone'] : 'N/A';
$license = !empty($data['license_no']) ? $data['license_no'] : 'N/A';
$address = !empty($data['address']) ? $data['address'] : 'N/A';
$bankName = !empty($data['bank_name']) ? $data['bank_name'] : 'N/A';
$accNoLast4 = !empty($data['account_no']) ? 'XXXX-XXXX-' . substr($data['account_no'], -4) : 'N/A';
$paymentDate = $data['payment_date'] ? date('Y-m-d H:i:s', strtotime($data['payment_date'])) : 'Pending';

$days = (strtotime($data['end_date']) - strtotime($data['start_date'])) / (60*60*24) + 1;
$pricePerDay = number_format($data['price_per_day'], 2);
$totalPrice = number_format($data['total_price'], 2);
$amountPaid = $data['paid_amount'] ? number_format($data['paid_amount'], 2) : '0.00';

// Build the text receipt
$receipt = "============================================================\n";
$receipt .= "              CAR RENTAL SYSTEM - RENTAL RECEIPT\n";
$receipt .= "============================================================\n";
$receipt .= "Receipt ID: #PAY-" . $paymentIdPad . "               Date: " . date('Y-m-d H:i:s') . "\n";
$receipt .= "Booking ID: #BK-" . $bookingIdPad . "                Status: " . strtoupper($data['booking_status']) . "\n";
$receipt .= "============================================================\n\n";

$receipt .= "[ CUSTOMER DETAILS ]\n";
$receipt .= "------------------------------------------------------------\n";
$receipt .= "Full Name:         " . $fullName . "\n";
$receipt .= "Email Address:     " . $data['email'] . "\n";
$receipt .= "Phone Number:      " . $phone . "\n";
$receipt .= "License Number:    " . $license . "\n";
$receipt .= "Billing Address:   " . str_replace("\r\n", ", ", $address) . "\n\n";

$receipt .= "[ VEHICLE DETAILS ]\n";
$receipt .= "------------------------------------------------------------\n";
$receipt .= "Car Model:         " . $data['car_name'] . "\n";
$receipt .= "Brand/Make:        " . $data['brand'] . "\n";
$receipt .= "Seating Capacity:  " . $data['seats'] . " Seats\n";
$receipt .= "Price Per Day:     ₹" . $pricePerDay . " / day\n\n";

$receipt .= "[ RENTAL PERIOD ]\n";
$receipt .= "------------------------------------------------------------\n";
$receipt .= "Start Date:        " . date('Y-m-d', strtotime($data['start_date'])) . "\n";
$receipt .= "End Date:          " . date('Y-m-d', strtotime($data['end_date'])) . "\n";
$receipt .= "Total Duration:    " . $days . " Day(s)\n\n";

$receipt .= "[ PAYMENT SUMMARY ]\n";
$receipt .= "------------------------------------------------------------\n";
$receipt .= "Payment ID:        #PAY-" . $paymentIdPad . "\n";
$receipt .= "Transaction Date:  " . $paymentDate . "\n";
$receipt .= "Payment Status:    " . strtoupper($data['payment_status'] ?? 'PENDING') . "\n";
$receipt .= "Paid Via:          " . $bankName . " (A/C: " . $accNoLast4 . ")\n";
$receipt .= "------------------------------------------------------------\n";
$receipt .= "TOTAL BOOKING FEE:  ₹" . $totalPrice . "\n";
$receipt .= "TOTAL AMOUNT PAID:  ₹" . $amountPaid . "\n";
$receipt .= "============================================================\n";
$receipt .= "          Thank you for choosing Car Rental System!\n";
$receipt .= "                 Have a safe and happy journey!\n";
$receipt .= "============================================================\n";

// Set clean text/plain download headers
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="CarRental_Receipt_#' . $bookingIdPad . '.txt"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo $receipt;
exit();
?>
