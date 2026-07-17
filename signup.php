<?php
session_start();
include 'partials/_dbconnect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email    = $_POST["email"];
    $password = $_POST["password"];  

    $sql = "INSERT INTO users (username, email, password) 
            VALUES ('$username', '$email', '$password')";
    try {
        if (mysqli_query($conn, $sql)) {
            echo "<div class='alert success'>Signup successful! You can login now.</div>";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo "<div class='alert danger'>Error: This email is already registered. Please login.</div>";
        } else {
            echo "<div class='alert danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Signup</title>
  <style>
    * { box-sizing: border-box; }
    body {
      background: linear-gradient(135deg, #f4f7f6 0%, #e0e6ed 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      flex-direction: column;
    }
    
    .alert {
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
      font-weight: bold;
      width: 90%;
      max-width: 400px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      animation: slideDownFade 0.5s ease-out;
      position: absolute;
      top: 20px;
      z-index: 100;
    }
    .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert.danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    .container {
      background: #fff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
      width: 360px;
      animation: fadeIn 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    h2 {
      text-align: center;
      margin-top: 0;
      margin-bottom: 30px;
      color: #2c7a7b;
      font-size: 28px;
    }

    .input-group {
      position: relative;
      margin-bottom: 25px;
    }
    .input-group input {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e1e5eb;
      border-radius: 6px;
      outline: none;
      background: transparent;
      font-size: 15px;
      transition: all 0.3s ease;
    }
    .input-group input[name="password"] {
      padding-right: 60px; 
    }
    .input-group label {
      position: absolute;
      top: 50%;
      left: 15px;
      transform: translateY(-50%);
      color: #888;
      font-size: 15px;
      pointer-events: none;
      transition: 0.3s ease;
      background: #fff;
      padding: 0 5px;
    }
    .input-group input:focus, 
    .input-group input:not(:placeholder-shown) {
      border-color: #2c7a7b;
    }
    .input-group input:focus ~ label, 
    .input-group input:not(:placeholder-shown) ~ label {
      top: 0;
      font-size: 12px;
      color: #2c7a7b;
    }
    .input-group input:valid:not(:placeholder-shown) {
      border-color: #4CAF50;
    }

    .toggle-password {
      position: absolute;
      top: 50%;
      right: 15px;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 13px;
      font-weight: bold;
      user-select: none;
      color: #666;
      transition: color 0.2s;
      background: #fff;
    }
    .toggle-password:hover {
      color: #2c7a7b;
    }

    button {
      width: 100%;
      padding: 14px;
      background: #2c7a7b;
      border: none;
      color: #fff;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s, transform 0.1s;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 10px;
    }
    button:hover { background: #225e5f; }
    button:active { transform: scale(0.98); }
    button:disabled { background: #71b1b2; cursor: not-allowed; }

    .loader {
      display: none;
      border: 3px solid rgba(255,255,255,0.3);
      border-top: 3px solid white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      animation: spin 1s linear infinite;
      margin-left: 10px;
    }

    .login-link {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
    }
    .login-link a {
      color: #2c7a7b;
      text-decoration: none;
      font-weight: bold;
      transition: color 0.3s;
    }
    .login-link a:hover {
      color: #1a494a;
      text-decoration: underline;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideDownFade {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  
  <div class="container">
    <h2>Create Account</h2>
    <form method="post" onsubmit="handleFormSubmit()">
      
      <div class="input-group">
        <input type="text" id="username" name="username" placeholder=" " required>
        <label for="username">Username</label>
      </div>

      <div class="input-group">
        <input type="email" id="email" name="email" placeholder=" " required>
        <label for="email">Email Address</label>
      </div>

      <div class="input-group">
        <input type="password" id="password" name="password" placeholder=" " required>
        <label for="password">Password</label>
        <span class="toggle-password" id="toggleText" onclick="togglePassword()">Show</span>
      </div>

      <button type="submit" id="signupBtn">
        <span id="btnText">Signup</span>
        <div class="loader" id="btnLoader"></div>
      </button>
    </form>
    
    <div class="login-link">
      <a href="login.php">Already have an account? Login here</a>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passField = document.getElementById('password');
      const toggleText = document.getElementById('toggleText');
      if (passField.type === "password") {
        passField.type = "text";
        toggleText.innerText = "Hide";
      } else {
        passField.type = "password";
        toggleText.innerText = "Show";
      }
    }

    function handleFormSubmit() {
      const btn = document.getElementById("signupBtn");
      const btnText = document.getElementById("btnText");
      const loader = document.getElementById("btnLoader");

      btnText.innerText = "Creating Account...";
      loader.style.display = "block";
      
      setTimeout(() => {
        btn.disabled = true;
      }, 50);
    }

    // Check for the success message and redirect
    document.addEventListener("DOMContentLoaded", function() {
      // Look for the element with classes 'alert' and 'success'
      const successMessage = document.querySelector('.alert.success');
      
      if (successMessage) {
        // Change the text slightly so the user knows what is happening
        successMessage.innerText = "Signup successful! Redirecting to login...";
        
        // Redirect to login.php after a 1.5 second delay
        setTimeout(() => {
          window.location.href = "login.php";
        }, 1500);
      }
    });
  </script>
</body>
</html>