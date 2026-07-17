<?php
session_start();
include 'partials/_dbconnect.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    $sql = "SELECT id, username FROM users WHERE email='$email' AND username='$username' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Setup a secure session token to allow password reset
        $_SESSION['reset_user_id'] = $row['id'];
        $_SESSION['reset_username'] = $row['username'];
        
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Verification failed! Incorrect Email or Username.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password | Car Rental</title>
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
        .forgot-box {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .forgot-box h2 {
            margin-bottom: 10px;
            color: #2c7a7b;
        }
        .forgot-box p {
            color: #666;
            margin-bottom: 30px;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
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
        .btn-verify {
            width: 100%;
            padding: 14px;
            background: #e67e22;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-verify:hover {
            background: #cf711f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 126, 34, 0.2);
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
        <div class="forgot-box">
            <h2>Forgot Password?</h2>
            <p>For security, please verify your identity by entering your registered Email Address and Username.</p>
            
            <?php if (!empty($error)): ?>
                <div class="error-msg"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label>Registered Email</label>
                    <input type="email" name="email" placeholder="e.g. user@example.com" required>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="e.g. johndoe" required>
                </div>

                <button type="submit" class="btn-verify">Verify Identity</button>
            </form>

            <div class="footer-links">
                Remember your password? <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
