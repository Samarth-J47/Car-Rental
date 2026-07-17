<?php
session_start();
include 'partials/_dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, email, password, role FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if ($password === $row['password']) { 
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role'];
            header("Location: " . ($row['role'] === 'admin' ? "admin/dashboard.php" : "index.php"));
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | Car Rental</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            margin: 0; padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-box h2 {
            margin-bottom: 30px;
            color: #2c7a7b;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
            position: relative;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        .form-group input:focus {
            border-color: #2c7a7b;
            box-shadow: 0 0 8px rgba(44, 122, 123, 0.2);
            outline: none;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #2c7a7b;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: #235e5e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 122, 123, 0.2);
        }
        .toggle-btn {
            position: absolute;
            right: 12px;
            top: 38px;
            background: none;
            border: none;
            color: #2c7a7b;
            font-size: 12px;
            cursor: pointer;
            font-weight: bold;
        }
        .error-msg {
            color: #e53e3e;
            background: #fff5f5;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid #feb2b2;
        }
        .footer-links {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .footer-links a {
            color: #2c7a7b;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'partials/_nav.php'; ?>

    <div class="main-content">
        <div class="login-box">
            <h2>Welcome Back</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-msg"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="e.g. user@example.com" autocomplete="off" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    <button type="button" class="toggle-btn" onclick="togglePass()">SHOW</button>
                </div>

                <button type="submit" class="btn-login">Login to Account</button>
            </form>

            <div class="footer-links">
                Don't have an account? <a href="signup.php">Signup here</a><br><br>
                <a href="forgot_password.php" style="color:#e67e22;">Forgot Password?</a>
            </div>
        </div>
    </div>

    <script>
        function togglePass() {
            const p = document.getElementById('password');
            const b = event.target;
            if (p.type === "password") {
                p.type = "text";
                b.innerText = "HIDE";
            } else {
                p.type = "password";
                b.innerText = "SHOW";
            }
        }
    </script>
</body>
</html>