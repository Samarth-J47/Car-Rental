<?php
session_start();
include 'partials/_dbconnect.php';

// Security Check: Make sure they came from forgot_password.php
if (!isset($_SESSION['reset_user_id'])) {
    header("Location: forgot_password.php");
    exit();
}

$error = "";
$success = "";
$reset_user_id = intval($_SESSION['reset_user_id']);
$reset_username = htmlspecialchars($_SESSION['reset_username']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $error = "Both password fields are required!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match! Please try again.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Update password in the database
        // NOTE: In a real-world app, use password_hash(). This project uses plain text passwords for simplicity.
        $new_password_escaped = mysqli_real_escape_string($conn, $new_password);
        $sql = "UPDATE users SET password='$new_password_escaped' WHERE id=$reset_user_id";
        
        if (mysqli_query($conn, $sql)) {
            // Success! Clear the reset token
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['reset_username']);
            $success = "Password reset successfully! You can now login with your new password.";
        } else {
            $error = "Error updating password. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password | Car Rental</title>
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
        .reset-box {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .reset-box h2 {
            margin-bottom: 10px;
            color: #2c7a7b;
        }
        .reset-box p {
            color: #666;
            margin-bottom: 30px;
            font-size: 0.95rem;
            line-height: 1.5;
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
        .btn-reset {
            width: 100%;
            padding: 14px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-reset:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.2);
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
        .success-msg {
            color: #155724;
            background: #d4edda;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 15px;
            border: 1px solid #c3e6cb;
            font-weight: bold;
        }
        .btn-login-back {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #2c7a7b;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'partials/_nav.php'; ?>

    <div class="main-content">
        <div class="reset-box">
            <h2>Create New Password</h2>
            
            <?php if (!empty($success)): ?>
                <div class="success-msg"><?= $success ?></div>
                <a href="login.php" class="btn-login-back">Go to Login</a>
            <?php else: ?>
                <p>Hello <strong><?= $reset_username ?></strong>, please enter your new secure password below.</p>

                <?php if (!empty($error)): ?>
                    <div class="error-msg"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" autocomplete="off">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" id="n_pass" placeholder="Enter new password" required>
                        <button type="button" class="toggle-btn" onclick="togglePass('n_pass', event)">SHOW</button>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" id="c_pass" placeholder="Confirm new password" required>
                        <button type="button" class="toggle-btn" onclick="togglePass('c_pass', event)">SHOW</button>
                    </div>

                    <button type="submit" class="btn-reset">Update Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function togglePass(id, event) {
            const p = document.getElementById(id);
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
