<?php
include '../partials/_dbconnect.php';  

$message = "";
$messageType = "";

// Update car price and seats
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $price_per_day = $_POST['price_per_day'];
    $seats = $_POST['seats'];
    if (mysqli_query($conn, "UPDATE cars SET price_per_day='$price_per_day', seats='$seats' WHERE id=$id")) {
        $message = "Car updated successfully!";
        $messageType = "warning";
    } else {
        $message = "Error updating car: " . mysqli_error($conn);
        $messageType = "danger";
    }
}

// Delete car
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    if (mysqli_query($conn, "DELETE FROM cars WHERE id=$id")) {
        $message = "Car deleted successfully!";
        $messageType = "danger";
    } else {
        $message = "Error deleting car: " . mysqli_error($conn);
        $messageType = "danger";
    }
}

// Handle search
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $result = mysqli_query($conn, "SELECT * FROM cars WHERE car_name LIKE '%$search%' OR brand LIKE '%$search%'");
} else {
    $result = mysqli_query($conn, "SELECT * FROM cars");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Cars</title>
    <style>
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #2c7a7b;
            margin-bottom: 20px;
        }
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success { background: #d4edda; color: #155724; }
        .warning { background: #fff3cd; color: #856404; }
        .danger { background: #f8d7da; color: #721c24; }
        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-bar input {
            padding: 8px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-bar button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            background: #2c7a7b;
            color: #fff;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #333;
            color: #fff;
        }
        .btn-warning { background: #ffc107; color: #000; padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; }
        .btn-danger { background: #dc3545; color: #fff; padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; }
    </style>
</head>
<body>

<h2>Manage Cars</h2>

<?php if (!empty($message)): ?>
    <div class="alert <?= $messageType; ?>">
        <?= $message; ?>
    </div>
<?php endif; ?>

<!-- Search bar -->
<div class="search-bar">
    <form method="get" action="">
        <input type="text" name="search" placeholder="Search by name or brand" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>
</div>

<table>
    <tr>
        <th>ID</th><th>Name</th><th>Brand</th><th>Price per Day</th><th>Seats</th><th>Actions</th>
    </tr>
    <?php if (mysqli_num_rows($result) > 0) { ?>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['car_name'] ?></td>
                <td><?= $row['brand'] ?></td>
                <td>₹<?= $row['price_per_day'] ?></td>
                <td><?= $row['seats'] ?></td>
                <td>
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="number" step="0.01" name="price_per_day" value="<?= $row['price_per_day'] ?>" placeholder="Price" required style="width:100px;">
                        <input type="number" name="seats" value="<?= $row['seats'] ?>" placeholder="Seats" required style="width:70px;">
                        <button type="submit" name="update" class="btn-warning">Update</button>
                    </form>

                    <form method="post" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this car?');">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" name="delete" class="btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr><td colspan="5">No cars found.</td></tr>
    <?php } ?>
</table>


<!-- Back button -->
<button onclick="window.location.href='dashboard.php'" 
        style="margin-top:20px; background:#6c757d; color:#fff; padding:10px 18px; border:none; border-radius:5px; cursor:pointer;">
  ⬅ Back to Dashboard
</button>

</body>
</html>
