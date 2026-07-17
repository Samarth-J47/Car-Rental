<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<style>
  nav {
    background: #222;
    padding: 18px 30px;
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
  }
  nav .navbar-brand {
    color: #fff;
    font-size: 22px;
    font-weight: bold;
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: opacity 0.3s;
  }
  nav .navbar-brand:hover {
    opacity: 0.9;
  }
  nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
  }
  nav ul li {
    margin-left: 20px;
  }
  nav ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    padding: 8px 15px;
    border-radius: 6px;
    transition: all 0.3s ease;
    font-weight: 500;
  }
  nav ul li a:hover {
    background: #444;
    transform: translateY(-2px);
  }

  .menu-toggle {
    display: none;
    font-size: 22px;
    color: #fff;
    background: none;
    border: none;
    cursor: pointer;
    position: absolute;
    right: 20px;
    top: 12px;
  }

  @media (max-width: 768px) {
    nav ul {
      flex-direction: column;
      align-items: flex-start;
      overflow: hidden;
      max-height: 0;
      opacity: 0;
      width: 100%;
      background: #333;
    }
    nav ul.show {
      max-height: 500px;
      opacity: 1;
    }
    .menu-toggle {
      display: block;
    }
  }
</style>

<nav>
  <a class="navbar-brand" href="/carrental/index.php">
    <img src="/carrental/images/logo.png" alt="Logo" style="height: 30px; vertical-align: middle; margin-right: 8px;">
    Car Rental
  </a>
  <button class="menu-toggle" onclick="toggleMenu()">☰</button>
  <ul id="nav-links">
    <li><a href="/carrental/index.php">Home</a></li>

    <?php if (!isset($_SESSION['user_id'])): ?>
      <!-- Guest links -->
      <li><a href="/carrental/signup.php">Signup</a></li>
      <li><a href="/carrental/login.php">Login</a></li>
    <?php else: ?>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <!-- Admin-only links -->
        <li><a href="/carrental/admin/dashboard.php">Admin Dashboard</a></li>
        <li><a href="/carrental/admin/manage_cars.php">Manage Cars</a></li>
        <li><a href="/carrental/admin/manage_bookings.php">Manage Bookings</a></li>
        <li><a href="/carrental/admin/admin_payments.php">Payment Records</a></li>
        <li><a href="/carrental/logout.php">Logout</a></li>
      <?php else: ?>
        <!-- User-only links -->
        <li><a href="/carrental/my_bookings.php">My Bookings</a></li>
        <li><a href="/carrental/payment_history.php">Payment History</a></li>
        <li><a href="/carrental/profile.php">My Profile</a></li>
        <li><a href="/carrental/logout.php">Logout</a></li>
      <?php endif; ?>
    <?php endif; ?>
  </ul>
</nav>

<script>
  function toggleMenu() {
    document.getElementById('nav-links').classList.toggle('show');
  }
</script>
