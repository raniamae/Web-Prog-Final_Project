<?php
session_start();
include "config.php";

$error = "";

// 1. Check if user submitted the form
if (isset($_POST['login'])) {
    
    // 2. Sanitize input to prevent SQL Injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // 3. Check if input is empty
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // 4. Query the database
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // 5. Verify Password
            if ($password === $user['password']) {
                
                // 6. Login Success: Set Session Variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];
				$_SESSION['avatar_url'] = $user['avatar_url'];
                
                // 7. Redirect to Home Page
                header("Location: index.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MovieSite</title>
    <link rel="stylesheet" href="final.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body class="auth-page">

    <a href="index.php" class="back-home">
        <i class="ri-arrow-left-line"></i> &nbsp; Back to Home
    </a>

    <div class = container>
        <div class="face-container">
            <div class="face">
                <div class="blush left-blush"></div>
                <div class="blush right-blush"></div>
                <div class="sunglasses">
                    <div class="lens left"></div>
                    <div class="bridge"></div>
                    <div class="lens right"></div>
                </div>
                <div class="eye-wrapper">
                    <div class="lashes">
                        <div class="lash"></div>
                        <div class="lash"></div>
                        <div class="lash"></div>
                    </div>
                    <div class="eye">
                        <div class="pupil"></div>
                    </div>
                </div>
                <div class="eye-wrapper">
                    <div class="lashes"> 
                        <div class="lash"></div>
                        <div class="lash"></div>
                        <div class="lash"></div>
                    </div>
                    <div class="eye">
                        <div class="pupil"></div>
                    </div>
                </div>
            </div>
        </div>

    <div class="login-container">
        <div class="login-header">
            <h2>Welcome Back</h2>
            <p>Please enter your details to sign in</p>
            
        <?php if ($error): ?>
            <div class="error-msg">
                <i class="ri-error-warning-line"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" name="login" class="login-submit-btn">Sign In</button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>

      <script>
// Eyes follow mouse
document.addEventListener("mousemove", function (e) {
    const eyes = document.querySelectorAll(".eye");

    eyes.forEach(eye => {
        const pupil = eye.querySelector(".pupil");
        if (!pupil) return;
        
        const rect = eye.getBoundingClientRect();
        const eyeX = rect.left + rect.width / 2;
        const eyeY = rect.top + rect.height / 2;

        const dx = e.clientX - eyeX;
        const dy = e.clientY - eyeY;
        const angle = Math.atan2(dy, dx);
        
        const maxOffset = 15;
        const offsetX = Math.cos(angle) * maxOffset;
        const offsetY = Math.sin(angle) * maxOffset;

        pupil.style.left = (50 + offsetX / (rect.width / 2) * 50) + '%';
        pupil.style.top = (50 + offsetY / (rect.height / 2) * 50) + '%';
    });
});

// Show sunglasses when typing password
const passwordInput = document.getElementById('password');
const sunglasses = document.querySelector('.sunglasses');

if (passwordInput && sunglasses) {
    passwordInput.addEventListener('focus', () => {
        sunglasses.classList.add('visible');
    });

    passwordInput.addEventListener('blur', () => {
        sunglasses.classList.remove('visible');
    });
}
</script>

</body>
</html>