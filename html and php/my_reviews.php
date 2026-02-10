<?php
session_start();
include "config.php";
include "tmdb_helper.php"; // Include this to get TMDB poster URLs

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle delete review
if (isset($_POST['action']) && $_POST['action'] == 'delete_review') {
    header('Content-Type: application/json');
    
    $review_id = intval($_POST['review_id']);
    $movie_id = intval($_POST['movie_id']);
    
    // Verify ownership
    $check_sql = "SELECT review_id FROM reviews WHERE review_id = $review_id AND user_id = $user_id";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    // Delete review
    $delete_sql = "DELETE FROM reviews WHERE review_id = $review_id";
    
    if (mysqli_query($conn, $delete_sql)) {
        // Recalculate average rating
        $avg_sql = "SELECT AVG(rating) as new_avg FROM reviews WHERE movie_id = $movie_id";
        $avg_result = mysqli_query($conn, $avg_sql);
        
        if (mysqli_num_rows($avg_result) > 0) {
            $avg_data = mysqli_fetch_assoc($avg_result);
            if ($avg_data['new_avg'] !== null) {
                $new_rating = number_format((float)$avg_data['new_avg'], 1, '.', '');
            } else {
                $new_rating = 0;
            }
        } else {
            $new_rating = 0;
        }
        
        // Update movie rating
        $update_movie_sql = "UPDATE movies SET average_rating = $new_rating WHERE movie_id = $movie_id";
        mysqli_query($conn, $update_movie_sql);
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
}

// Handle update review
if (isset($_POST['action']) && $_POST['action'] == 'update_review') {
    header('Content-Type: application/json');
    
    $review_id = intval($_POST['review_id']);
    $rating = floatval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    // Verify ownership
    $check_sql = "SELECT movie_id FROM reviews WHERE review_id = $review_id AND user_id = $user_id";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $row = mysqli_fetch_assoc($check_result);
    $movie_id = $row['movie_id'];
    
    // Update review
    $update_sql = "UPDATE reviews SET rating = $rating, comment = '$comment' WHERE review_id = $review_id";
    
    if (mysqli_query($conn, $update_sql)) {
        // Recalculate average rating
        $avg_sql = "SELECT AVG(rating) as new_avg FROM reviews WHERE movie_id = $movie_id";
        $avg_result = mysqli_query($conn, $avg_sql);
        $avg_data = mysqli_fetch_assoc($avg_result);
        $new_rating = number_format((float)$avg_data['new_avg'], 1, '.', '');
        
        // Update movie rating
        $update_movie_sql = "UPDATE movies SET average_rating = $new_rating WHERE movie_id = $movie_id";
        mysqli_query($conn, $update_movie_sql);
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
}

// Fetch all reviews by this user with movie details
$sql_reviews = "SELECT r.*, m.title, m.tmdb_id, m.release_year 
                FROM reviews r 
                JOIN movies m ON r.movie_id = m.movie_id 
                WHERE r.user_id = $user_id 
                ORDER BY r.created_at DESC";
$reviews_result = mysqli_query($conn, $sql_reviews);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reviews</title>
    <link rel="stylesheet" href="final.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <main>
            <section class="movie collection-section">
                
                <div class="section-header">
                    <h2 class="section-heading collection-heading">
                        <i class="ri-chat-3-line icon-yellow"></i> My Reviews
                    </h2>
                </div>
            
            <?php if (mysqli_num_rows($reviews_result) > 0): ?>
                <div class="my-reviews-grid">
                    <?php while($review = mysqli_fetch_assoc($reviews_result)): 
                        // Get poster URL from TMDB
                        $mediaUrls = getTMDBMediaUrls($review['tmdb_id']);
                        $poster_url = $mediaUrls['poster_url'];
                    ?>
                        <div class="my-review-card" data-review-id="<?php echo $review['review_id']; ?>" data-movie-id="<?php echo $review['movie_id']; ?>">
                            <a href="movie_info.php?id=<?php echo $review['movie_id']; ?>" class="review-movie-link">
                                <img src="<?php echo $poster_url; ?>" alt="<?php echo htmlspecialchars($review['title']); ?>" class="review-movie-poster">
                                <div class="review-movie-info">
                                    <h3><?php echo htmlspecialchars($review['title']); ?></h3>
                                    <p class="review-year"><?php echo $review['release_year']; ?></p>
                                </div>
                            </a>
                            
                            <div class="review-details">
                                <div class="review-header">
                                    <div class="review-rating-display">
                                        <i class="ri-star-fill"></i> 
                                        <span class="review-rating"><?php echo $review['rating']; ?></span> / 10
                                    </div>
                                    <div class="review-actions">
                                        <button data-action="edit">
                                            <i class="ri-edit-line"></i> Edit
                                        </button>
                                        <button data-action="delete">
                                            <i class="ri-delete-bin-line"></i> Delete
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Display mode -->
                                <div class="review-display">
                                    <p class="review-text"><?php echo htmlspecialchars($review['comment']); ?></p>
                                </div>
                                
                                <!-- Edit mode (hidden by default) -->
                                <div class="review-edit-form" style="display: none;">
                                    <div style="margin-bottom: 10px;">
                                        <label for="edit-rating-<?php echo $review['review_id']; ?>" style="color: #b3b3b3; font-size: 12px;">Rating:</label>
                                        <select id="edit-rating-<?php echo $review['review_id']; ?>" class="edit-rating" style="background: var(--rich-black-fogra-29); color: white; padding: 8px; border-radius: 5px; border: 1px solid rgba(255,255,255,0.1); margin-left: 10px;">
                                            <?php for($i = 1; $i <= 10; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo ($review['rating'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <label for="edit-comment-<?php echo $review['review_id']; ?>" class="visually-hidden">Review comment</label>
                                    <textarea id="edit-comment-<?php echo $review['review_id']; ?>" class="edit-comment" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 10px; border-radius: 8px; min-height: 80px; resize: vertical; font-family: inherit;"><?php echo htmlspecialchars($review['comment']); ?></textarea>
                                    <div style="margin-top: 10px; display: flex; gap: 10px;">
                                        <button type="button" class="save-edit-btn" style="padding: 8px 20px; background: var(--light-azure); color: white; border: none; border-radius: 5px; cursor: pointer;">Save</button>
                                        <button type="button" class="cancel-edit-btn" style="padding: 8px 20px; background: rgba(255,255,255,0.1); color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                                    </div>
                                </div>
                                
                                <p class="review-date">
                                    <i class="ri-time-line"></i> 
                                    <?php echo date('F j, Y', strtotime($review['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="movie-grid">
                    <div class="empty-state">
                        <i class="ri-chat-3-line empty-icon"></i>
                        <p class="empty-title">No reviews yet</p>
                        <p class="empty-desc">Start reviewing movies to see them here!</p>
                        <a href="index.php" class="browse-btn">Browse Movies</a>
                    </div>
                </div>
            <?php endif; ?>
            </section>
        </main>
    </div>

    <script src="final.js"></script>

</body>
</html>