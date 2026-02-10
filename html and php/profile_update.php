<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "config.php";

// check if user login or not
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";
$error = "";

// 2. get user current data
$sql_user = "SELECT * FROM users WHERE user_id = $user_id";
$result_user = mysqli_query($conn, $sql_user);
$current_user = mysqli_fetch_assoc($result_user);


if (isset($_POST['update_profile'])) {

    // Accept input(if it empty keep original data)
    $new_username = !empty($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : $current_user['username'];
    $new_email = !empty($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : $current_user['email'];
    $new_password = !empty($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : $current_user['password'];
    
    // set avatar as default
    $new_avatar_url = $current_user['avatar_url'];

    // deal will upload image (will only execute when image is uploaded ) ---
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');

        if (in_array($file_ext, $allowed)) {
            $new_filename = "avatar_" . $user_id . "_" . time() . "." . $file_ext; 
            $target_file = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
                $new_avatar_url = $target_file;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file format.";
        }
    }

    if (empty($error)) {
        // update database
        $update_sql = "UPDATE users SET username='$new_username', email='$new_email', password='$new_password', avatar_url='$new_avatar_url' WHERE user_id=$user_id";
        
        if (mysqli_query($conn, $update_sql)) {
            $msg = "Profile updated successfully!";
            
            // update Session immediate，so header pic, username and email will got updated quickly
            $_SESSION['user_name'] = $new_username;
            $_SESSION['user_email'] = $new_email;
            $_SESSION['avatar_url'] = $new_avatar_url;
            
            // re-fetched data so it shoes the latest status
            $current_user['username'] = $new_username;
            $current_user['email'] = $new_email;
            $current_user['avatar_url'] = $new_avatar_url;

            // refresh page
            // header("Refresh:1"); 
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="final.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
       
        body {
           
            display: flex; flex-direction: column; min-height: 100vh;
        }
        .update-container {
            margin: 10px auto 50px; /* 避開 header */
            background: var(--oxford-blue);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #ccc; margin-bottom: 8px; font-size: 14px; }
        .form-group input {
            width: 100%; padding: 12px 15px;
            background: #0d1117; border: 1px solid #2a3b55;
            border-radius: 10px; color: white; box-sizing: border-box;
        }
        .current-avatar {
            width: 100px; height: 100px; border-radius: 50%; 
            object-fit: cover; display: block; margin: 0 auto 20px;
            border: 3px solid var(--yellow);
        }
        .btn-submit {
            width: 100%; padding: 12px; background: var(--light-azure);
            color: white; border: none; border-radius: 10px; cursor: pointer;
            font-weight: bold; font-size: 16px;
        }
        .btn-submit:hover { background: var(--azure); }
        .msg-box { padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .msg-success { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .msg-error { background: rgba(239, 68, 68, 0.2); color: #f87171; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="update-container">
        <h2 style="text-align: center; margin-bottom: 30px;">Update Profile</h2>

        <?php 
            $display_img = !empty($current_user['avatar_url']) ? $current_user['avatar_url'] : "img/default_avatar.png";
        ?>
        <img src="<?php echo $display_img; ?>" alt="Current Avatar" class="current-avatar">

        <?php if($msg): ?><div class="msg-box msg-success"><?php echo $msg; ?></div><?php endif; ?>
        <?php if($error): ?><div class="msg-box msg-error"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            
           <div class="form-group">
                <label for="avatar">Change Avatar (Optional)</label>
                <input type="file" name="avatar" id="avatar" accept="image/*" style="border:none; padding-left:0;">
            </div>

            <div class="form-group">
                <label for="username">Username (Current: <?php echo htmlspecialchars($current_user['username']); ?>)</label>
                <input type="text" name="username" id="username" placeholder="Leave empty to keep current">
            </div>

            <div class="form-group">
                <label for="email">Email (Current: <?php echo htmlspecialchars($current_user['email']); ?>)</label>
                <input type="email" name="email" id="email" placeholder="Leave empty to keep current">
            </div>

            <div class="form-group">
                <label for="password">New Password (Optional)</label>
                <input type="password" name="password" id="password" placeholder="Leave empty to keep current password">
            </div>

            <button type="submit" name="update_profile" class="btn-submit">Save Changes</button>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="index.php" style="color: #ccc; font-size: 14px;">Cancel</a>
            </div>
        </form>
    </div>

    <script src="final.js"></script>
</body>
</html>