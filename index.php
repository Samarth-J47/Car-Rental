<?php
session_start();
include __DIR__ . '/partials/_dbconnect.php';

// Fetch cars from database
$sql = "SELECT * FROM cars";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM cars WHERE brand LIKE '%$search%' OR car_name LIKE '%$search%'";
}
$result = mysqli_query($conn, $sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Car Rental Project</title>
  <style>
    body { 
      background: #f4f4f9; 
      font-family: Arial, sans-serif; 
      margin: 0; padding: 0; 
      color: #333;
    }
    .container { max-width: 1100px; margin: 0 auto; padding: 20px; }
    
    header { 
      background: #2c7a7b; 
      color: white; 
      padding: 40px 20px; 
      text-align: center; 
      margin-bottom: 30px;
    }
    header h1 { margin: 0; font-size: 2.2rem; }
    header p { margin: 10px 0 0; opacity: 0.9; }

    .car-grid { 
      display: flex; 
      flex-wrap: wrap; 
      gap: 20px; 
      justify-content: center; 
    }
    .car-card { 
      background: white; 
      border: 1px solid #e2e8f0; 
      border-top: 4px solid #2c7a7b;
      border-radius: 12px; 
      width: 280px; 
      padding: 20px; 
      text-align: center; 
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .car-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px rgba(0,0,0,0.1); }
    .car-img { width: 100%; height: 160px; object-fit: cover; border-radius: 5px; }
    .car-card h3 { margin: 15px 0 5px; color: #2c7a7b; }
    .car-card p { margin: 5px 0; color: #666; font-size: 0.9rem; }
    .price { font-weight: bold; color: #e67e22; font-size: 1.1rem; margin: 10px 0; }
    
    .btn { 
      display: inline-block; 
      background: #2c7a7b; 
      color: white; 
      padding: 10px 20px; 
      text-decoration: none; 
      border-radius: 5px; 
      border: none;
      cursor: pointer;
      font-weight: bold;
      width: 100%;
      box-sizing: border-box;
    }
    .btn:hover { background: #235e5e; }

    /* Modal */
    #bookingModal { 
      display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
      background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 9999;
    }
    .modal-content { background: white; padding: 25px; border-radius: 10px; width: 350px; }
    .form-group { margin-bottom: 15px; text-align: left; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
    
    footer { text-align: center; padding: 40px; color: #888; font-size: 0.9rem; }
  </style>
</head>
<body>
  <?php include 'partials/_nav.php'; ?>

  <header style="margin-top: 20px;">
    <div class="container">
      <img src="images/logo.png" alt="Logo" style="height: 80px; margin-bottom: 15px;">
      <h1>Car Rental System</h1>
      <p>Car rental service.</p>
    </div>
  </header>

  <div class="container">
    <?php if (!isset($_SESSION['user_id'])): ?>
      <!-- Guest View: Promotional "Ads" -->
      <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 15px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-top: 20px; transition: transform 0.3s ease;">
        <h2 style="color: #2c7a7b; margin-bottom: 20px;">Unlock Our Exclusive Fleet</h2>
        <p style="font-size: 1.1rem; color: #666; max-width: 600px; margin: 0 auto 30px;">
          Join thousands of happy drivers! Sign up today to browse our premium collection of cars, get exclusive weekend deals, and book your next journey in seconds.
        </p>
        
        <div style="display: flex; justify-content: center; gap: 50px; margin-bottom: 40px; flex-wrap: wrap;">
          <div class="promo-item" style="width: 200px; cursor: default;">
            <div style="font-size: 2.5rem; margin-bottom: 15px;">⚡</div>
            <h4 style="margin: 0; color: #2d3748;">Quick Booking</h4>
            <p style="font-size: 0.85rem; color: #888;">Book your car in less than 2 minutes.</p>
          </div>
          <div class="promo-item" style="width: 200px; cursor: default;">
            <div style="font-size: 2.5rem; margin-bottom: 15px;">💎</div>
            <h4 style="margin: 0; color: #2d3748; padding-top: 5px;">Luxury Fleet</h4>
            <p style="font-size: 0.85rem; color: #888;">From sedans to premium SUVs.</p>
          </div>
        </div>

        <style>
          .btn-get-started:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(44, 122, 123, 0.3); }
        </style>

        <a href="signup.php" class="btn btn-get-started" style="width: auto; padding: 15px 50px; font-size: 1.2rem; transition: all 0.3s ease; display: inline-block;">Get Started - Create Account</a>
        <p style="margin-top: 20px; font-size: 0.9rem;">Already a member? <a href="login.php" style="color: #2c7a7b; font-weight: bold; text-decoration: none;">Login here</a></p>
      </div>

    <?php else: ?>
      <!-- User View: The Car List -->
      <h2 style="text-align: center; margin-bottom: 15px; color: #2c7a7b;">Choose Your Ride</h2>

      <!-- Search Bar -->
      <div style="text-align: center; margin-bottom: 30px;">
        <form action="index.php" method="GET" style="display: flex; justify-content: center; gap: 10px;">
          <input type="text" name="search" placeholder="Search by brand or car name..." 
                 value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                 style="padding: 10px 15px; width: 300px; border-radius: 25px; border: 1px solid #ccc; font-size: 1rem; outline: none;">
          <button type="submit" class="btn" style="width: auto; padding: 10px 25px; border-radius: 25px;">Search</button>
          <?php if(isset($_GET['search']) && !empty(trim($_GET['search']))): ?>
            <a href="index.php" class="btn" style="width: auto; padding: 10px 25px; background: #6c757d; border-radius: 25px; text-decoration: none;">Clear</a>
          <?php endif; ?>
        </form>
      </div>
      <div class="car-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="car-card">
              <img src="images/<?= $row['image'] ?>" class="car-img" alt="<?= $row['car_name'] ?>">
              <h3><?= htmlspecialchars($row['car_name']) ?></h3>
              <p><?= htmlspecialchars($row['brand']) ?> • <?= $row['seats'] ?> Seats</p>
              <div class="price">₹<?= $row['price_per_day'] ?> / Day</div>
              <button class="btn" onclick="openModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['car_name']) ?>', <?= $row['price_per_day'] ?>)">Book Now</button>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No cars available at the moment.</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Modal -->
  <div id="bookingModal">
    <div class="modal-content">
      <h3 id="modalTitle">Book Car</h3>
      <form action="book.php" method="POST">
        <input type="hidden" name="car_id" id="m_car_id">
        <input type="hidden" id="m_price">
        
        <div class="form-group">
          <label>Start Date</label>
          <input type="date" name="start_date" id="s_date" required>
        </div>
        <div class="form-group">
          <label>End Date</label>
          <input type="date" name="end_date" id="e_date" required>
        </div>
        
        <div id="estimate" style="margin-bottom: 15px; font-weight: bold; color: #2c7a7b;">
          Estimated Total: ₹0
        </div>
        
        <button type="submit" id="bookBtn" class="btn" disabled>Confirm Booking</button>
        <button type="button" onclick="closeModal()" style="background: none; border: none; color: #999; cursor: pointer; margin-top: 10px; width: 100%;">Cancel</button>
      </form>
    </div>
  </div>

  <footer>
    <div class="container">
      &copy; <?= date('Y') ?> Car Rental System. All rights reserved.
    </div>
  </footer>

  <script>
    function openModal(id, name, price) {
      document.getElementById('m_car_id').value = id;
      document.getElementById('m_price').value = price;
      document.getElementById('modalTitle').innerText = "Book " + name;
      document.getElementById('bookingModal').style.display = 'flex';
    }
    function closeModal() {
      document.getElementById('bookingModal').style.display = 'none';
    }
    function updateEstimate() {
      const s = document.getElementById('s_date').value;
      const e = document.getElementById('e_date').value;
      const p = document.getElementById('m_price').value;
      if (s && e) {
        const diff = (new Date(e) - new Date(s)) / (1000 * 60 * 60 * 24) + 1;
        if (diff > 0) {
          document.getElementById('estimate').innerText = "Estimated Total: ₹" + (diff * p);
          document.getElementById('bookBtn').disabled = false;
        } else {
          document.getElementById('estimate').innerText = "Estimated Total: ₹0";
          document.getElementById('bookBtn').disabled = true;
        }
      }
    }
    document.getElementById('s_date').addEventListener('change', updateEstimate);
    document.getElementById('e_date').addEventListener('change', updateEstimate);
  </script>
</body>
</html>
