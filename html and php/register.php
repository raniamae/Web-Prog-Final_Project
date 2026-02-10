<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();


if (!file_exists("config.php")) {
    die("Error: config.php not found! Please make sure it is in the same folder.");
}
include "config.php";

$error = "";
$success = "";


if (isset($_POST['register'])) {
    
    // accept and filter input data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    
    $avatar_url = "img/default-avatar.jpg";

    // check if the files are selected and there are no errors during the upload process
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        
        $upload_dir = "uploads/"; 
        
  
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // get file name
        $file_name = $_FILES['avatar']['name'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // allow ext 
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');

        if (in_array($file_ext, $allowed_ext)) {
            // generate unqiue file naame avoid duplicate file name 
            $new_filename = "avatar_" . uniqid() . "." . $file_ext;
            $target_file = $upload_dir . $new_filename;

            
            if (move_uploaded_file($file_tmp, $target_file)) {
                $avatar_url = $target_file; 
            } else {
                $error = "Failed to upload image. Please check folder permissions.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, GIF are allowed.";
        }
    }
  

    // 4. verify form
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!empty($error)) {
       
    } else {
        // check if the email is duplicated
        $check_sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "Username or Email already exists.";
        } else {
            
            $sql = "INSERT INTO users (username, email, password, avatar_url, created_at) VALUES ('$username', '$email', '$password', '$avatar_url', NOW())";
            
            if (mysqli_query($conn, $sql)) {
                $success = "Registration successful! Redirecting...";
                // go to login page after 2 sec
                header("refresh:2;url=login.php");
            } else {
                $error = "Database Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="final.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body class="auth-page">

    <a href="index.php" class="back-home"><i class="ri-arrow-left-line"></i> Back</a>

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

    <div class="register-container">
        <div class="login-header">
            <h2>Create Account</h2>
            <p>Join us to start your movie collection</p>
        </div>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-msg"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            
           <div class="file-input-group">
                <label for="avatar">Profile Picture (Optional)</label>
                <input type="file" name="avatar" id="avatar" accept="image/*">
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
            </div>

            <button type="submit" name="register" class="login-submit-btn">Register</button>
        </form>

        <div class="register-link">
            Already have an account? <a href="login.php">Login</a>
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

// Show sunglasses when typing password or confirm password
const passwordInput = document.querySelector('input[name="password"]');
const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
const sunglasses = document.querySelector('.sunglasses');

if (sunglasses) {
    if (passwordInput) {
        passwordInput.addEventListener('focus', () => {
            sunglasses.classList.add('visible');
        });

        passwordInput.addEventListener('blur', () => {
            sunglasses.classList.remove('visible');
        });
    }

    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('focus', () => {
            sunglasses.classList.add('visible');
        });

        confirmPasswordInput.addEventListener('blur', () => {
            sunglasses.classList.remove('visible');
        });
    }
}
</script>

</body>
</html>