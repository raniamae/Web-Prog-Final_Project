<?php
session_start();
include "config.php";

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_name'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add to collection']);
    exit;
}

// Get user_id
$user_id = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
} elseif (isset($_SESSION['user_name'])) {
    $user_name = mysqli_real_escape_string($conn, $_SESSION['user_name']);
    $user_sql = "SELECT user_id FROM users WHERE username = '$user_name'";
    $user_result = mysqli_query($conn, $user_sql);
    
    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user_row = mysqli_fetch_assoc($user_result);
        $user_id = intval($user_row['user_id']);
        $_SESSION['user_id'] = $user_id;
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
}

$movie_id = isset($_POST['movie_id']) ? intval($_POST['movie_id']) : 0;

if ($movie_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid movie']);
    exit;
}

// Check if already in collection
$check_sql = "SELECT * FROM collections WHERE user_id = $user_id AND movie_id = $movie_id";
$check_result = mysqli_query($conn, $check_sql);

if ($check_result && mysqli_num_rows($check_result) > 0) {
    // Already in collection - remove it
    $delete_sql = "DELETE FROM collections WHERE user_id = $user_id AND movie_id = $movie_id";
    if (mysqli_query($conn, $delete_sql)) {
        echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Removed from collection']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove: ' . mysqli_error($conn)]);
    }
} else {
    // Add to collection
    $insert_sql = "INSERT INTO collections (user_id, movie_id, added_at) VALUES ($user_id, $movie_id, NOW())";
    if (mysqli_query($conn, $insert_sql)) {
        echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Added to collection']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add: ' . mysqli_error($conn)]);
    }
}

mysqli_close($conn);
?>
