<?php
session_start();
include '../partials/_dbconnect.php';

$totalCars = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM cars"))['count'];
$totalBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM bookings"))['count'];
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM users"))['count'];
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <style>
    body {
      background-color: #f4f4f9;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }
    nav {
      background: #333;
      color: #fff;
      padding: 15px;
    }
    .container {
      max-width: 1100px;
      margin: 30px auto;
      padding: 0 20px;
    }
    h1 {
      text-align: center;
      margin-bottom: 30px;
      color: #2c7a7b;
    }
    .row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 30px;
      justify-content: center;
    }
    .card {
      flex: 1;
      min-width: 250px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      text-align: center;
      padding: 30px 20px;
    }
    .card h5 {
      margin-top: 0;
      margin-bottom: 10px;
      font-size: 20px;
      color: #333;
    }
    

    .stat-card h5 {
      color: inherit;
    }
    .stat-number {
      font-size: 36px;
      font-weight: bold;
      margin: 0;
    }

   
    .card-desc {
      font-size: 15px;
      color: #666;
      line-height: 1.5;
      margin-bottom: 20px;
      font-weight: normal;
    }

   
    .bg-primary { background-color: #007bff; color: #fff; }
    .bg-success { background-color: #28a745; color: #fff; }
    .bg-warning { background-color: #ffc107; color: #333; }
    
   
    .btn {
      display: inline-block;
      margin: 5px;
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      font-size: 14px;
      color: #fff;
      transition: background 0.3s ease;
    }
    .btn-primary { background-color: #007bff; }
    .btn-secondary { background-color: #6c757d; }
    .btn-primary:hover { background-color: #0056b3; }
    .btn-secondary:hover { background-color: #5a6268; }
  </style>
</head>
<body>
  <?php require '../partials/_nav.php'; ?>

  <div class="container">
    <h1>Admin Dashboard</h1>

    <div class="row">
      <div class="card stat-card bg-primary">
        <h5>Total Cars</h5>
        <p class="stat-number"><?= $totalCars ?></p>
      </div>
      <div class="card stat-card bg-success">
        <h5>Total Bookings</h5>
        <p class="stat-number"><?= $totalBookings ?></p>
      </div>
      <div class="card stat-card bg-warning">
        <h5>Total Users</h5>
        <p class="stat-number"><?= $totalUsers ?></p>
      </div>
    </div>

    <div class="row">
      <div class="card">
        <h5>Manage Cars</h5>
        <p class="card-desc">Add new cars or update existing ones.</p>
        <a href="add_car.php" class="btn btn-primary">Add Car</a>
        <a href="manage_cars.php" class="btn btn-secondary">View Cars</a>
      </div>
      <div class="card">
        <h5>Manage Bookings</h5>
        <p class="card-desc">View and manage customer bookings.</p>
        <a href="manage_bookings.php" class="btn btn-primary">View Bookings</a>
      </div>
      <div class="card">
        <h5>Manage Users</h5>
        <p class="card-desc">View registered users.</p>
        <a href="manage_users.php" class="btn btn-primary">View Users</a>
      </div>
    </div>
  </div>
</body>
</html>