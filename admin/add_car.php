<?php
session_start();
include '../partials/_dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $car_name = $_POST['car_name'];
    $brand = $_POST['brand'];
    $price_per_day = $_POST['price_per_day'];
    $status = $_POST['status'];
    $image = $_POST['image']; 
    $seats = $_POST['seats'];

    $sql = "INSERT INTO cars (car_name, brand, price_per_day, seats, status, image) 
            VALUES ('$car_name', '$brand', '$price_per_day', '$seats', '$status', '$image')";
    if (mysqli_query($conn, $sql)) {
        header("Location: manage_cars.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Car</title>
  <style>
    body {
      background-color: #f4f4f9;
      font-family: Arial, sans-serif;
    }
    .form-container {
      max-width: 500px;
      margin: 50px auto;
      padding: 25px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #2c7a7b;
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #2c7a7b;
      color: #fff;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
    }
    button:hover {
      background-color: #225e5f;
    }
    nav {
      background: #333;
      padding: 10px;
      color: #fff;
    }
  </style>
</head>
<body>
  <?php require '../partials/_nav.php'; ?>

  <div class="form-container">
  <h2>Add New Car</h2>
  <form method="POST">
    <div>
      <label>Car Name</label>
      <input type="text" name="car_name" required>
    </div>
    <div>
      <label>Brand</label>
      <input type="text" name="brand" required>
    </div>
    <div>
      <label>Price per Day</label>
      <input type="number" name="price_per_day" required>
    </div>
    <div>
      <label>Number of Seats</label>
      <input type="number" name="seats" value="5" required>
    </div>
    <div>
      <label>Status</label>
      <select name="status">
        <option value="available">Available</option>
        <option value="unavailable">Unavailable</option>
      </select>
    </div>
    <div>
      <label>Image Filename (e.g. honda_city.jpg)</label>
      <input type="text" name="image" required>
    </div>
    <button type="submit">Add Car</button>
  </form>

  <!-- Back button -->
  <button onclick="window.location.href='dashboard.php'" 
          style="margin-top:15px; background:#6c757d; color:#fff; padding:10px 18px; border:none; border-radius:5px; cursor:pointer;">
    ⬅ Back to Dashboard
  </button>
</div>

</body>
</html>