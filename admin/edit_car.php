<?php
include '../partials/_dbconnect.php';
$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM cars WHERE id=$id");
$car = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['car_name'];
    $brand = $_POST['brand'];
    $price = $_POST['price_per_day'];
    $status = $_POST['status'];

    $sql = "UPDATE cars SET car_name='$name', brand='$brand', price_per_day='$price', status='$status' WHERE id=$id";
    mysqli_query($conn, $sql);
    echo "<p style='color:green; text-align:center;'>Car updated successfully!</p>";
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Car</title>
  <style>
    body {
      background-color: #f4f4f9;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
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
  </style>
</head>
<body>
  <div class="form-container">
  <h2>Edit Car</h2>
  <form method="post">
    <label>Car Name:</label>
    <input type="text" name="car_name" value="<?= $car['car_name'] ?>" required>

    <label>Brand:</label>
    <input type="text" name="brand" value="<?= $car['brand'] ?>" required>

    <label>Price/Day:</label>
    <input type="number" name="price_per_day" value="<?= $car['price_per_day'] ?>" required>

    <label>Status:</label>
    <select name="status">
      <option value="available" <?= $car['status']=='available'?'selected':'' ?>>Available</option>
      <option value="booked" <?= $car['status']=='booked'?'selected':'' ?>>Booked</option>
    </select>

    <button type="submit">Update Car</button>
  </form>

  <!-- Back button to previous page -->
  <button onclick="history.back()" 
          style="margin-top:15px; background:#6c757d; color:#fff; padding:10px 18px; border:none; border-radius:5px; cursor:pointer;">
    ⬅ Back
  </button>
</div>

</body>
</html>