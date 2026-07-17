<?php
session_start();
include __DIR__ . '/partials/_dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    die("<p style='color:red;text-align:center;'>You must be logged in to book a car.</p>
         <p style='text-align:center;'><a href='index.php'>Back to Cars</a></p>");
}

$user_id = $_SESSION['user_id'];
// Check if profile is complete (including bank details)
$user_sql = "SELECT full_name, phone, address, license_no, bank_name, account_no FROM users WHERE id = $user_id";
$user_res = mysqli_query($conn, $user_sql);
$user_data = mysqli_fetch_assoc($user_res);

if (empty($user_data['full_name']) || empty($user_data['phone']) || empty($user_data['address']) || empty($user_data['license_no']) || empty($user_data['bank_name']) || empty($user_data['account_no'])) {
    header("Location: profile.php?msg=need_profile&redirect=book");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = intval($_POST['car_id']);
    $user_id = $_SESSION['user_id'];

    if (!isset($_POST['start_date']) || !isset($_POST['end_date'])) {
        die("<p style='color:red;text-align:center;'>Dates not selected.</p>
             <p style='text-align:center;'><a href='index.php'>⬅ Back to Cars</a></p>");
    }

    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];

    // Check overlap with confirmed bookings only
    $check_sql = "SELECT COUNT(*) AS cnt FROM bookings 
                  WHERE car_id = $car_id 
                  AND status = 'confirmed'
                  AND ('$start_date' <= end_date AND '$end_date' >= start_date)";
    $check_res = mysqli_query($conn, $check_sql);
    $check_row = mysqli_fetch_assoc($check_res);

    if ($check_row['cnt'] > 0) {
        echo "<p style='color:red;text-align:center;'>Sorry, this car is already booked for the selected dates.</p>";
        echo "<p style='text-align:center;'><a href='index.php'>⬅ Back to Cars</a></p>";
        exit;
    }

    // Fetch car's daily price
    $price_sql = "SELECT price_per_day FROM cars WHERE id = $car_id";
    $price_res = mysqli_query($conn, $price_sql);
    $car = mysqli_fetch_assoc($price_res);

    if (!$car) {
        die("<p style='color:red;text-align:center;'>Car not found.</p>
             <p style='text-align:center;'><a href='index.php'>⬅ Back to Cars</a></p>");
    }

    $price_per_day = $car['price_per_day'];

    // Calculate number of days
    $days = (strtotime($end_date) - strtotime($start_date)) / (60*60*24) + 1;
    if ($days <= 0) {
        die("<p style='color:red;text-align:center;'>Invalid date range.</p>
             <p style='text-align:center;'><a href='index.php'>⬅ Back to Cars</a></p>");
    }

    $total_price = $days * $price_per_day;

    // Insert booking request with status = pending
    $insert_sql = "INSERT INTO bookings (car_id, user_id, start_date, end_date, total_price, status) 
                   VALUES ($car_id, $user_id, '$start_date', '$end_date', $total_price, 'pending')";
    if (mysqli_query($conn, $insert_sql)) {
        echo "<p style='color:green;text-align:center;'>Booking request submitted! Waiting for admin approval.</p>";
        echo "<p style='text-align:center;'><a href='index.php'>⬅ Back to Cars</a></p>";
    } else {
        die("<p style='color:red;text-align:center;'>Error submitting booking request: " . mysqli_error($conn) . "</p>
             <p style='text-align:center;'><a href='index.php'>⬅ Back to Cars</a></p>");
    }
} else {
    die("<p style='color:red;text-align:center;'>Invalid request.</p>
         <p style='text-align:center;'><a href='index.php'>⬅ Back to Cars</a></p>");
}
?>
