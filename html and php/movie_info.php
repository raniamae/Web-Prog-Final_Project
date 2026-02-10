<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "config.php";
include "tmdb_helper.php";

if (isset($_POST['action']) && $_POST['action'] == 'update_review') {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
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
        
        echo json_encode([
            'success' => true,
            'new_average_rating' => $new_rating
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
}


if (isset($_POST['action']) && $_POST['action'] == 'delete_review') {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
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
        
        echo json_encode([
            'success' => true,
            'new_average_rating' => $new_rating
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
}

// Handle AJAX new review submission
if (isset($_POST['action']) && $_POST['action'] == 'submit_review') {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login to write a review']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $movie_id = intval($_POST['movie_id']);
    $rating = floatval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    if (empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a comment']);
        exit;
    }
    
    // Insert the new review
    $sql_review = "INSERT INTO reviews (user_id, movie_id, rating, comment) VALUES ($user_id, $movie_id, $rating, '$comment')";
    
    if (mysqli_query($conn, $sql_review)) {
        // Calculate average
        $avg_sql = "SELECT AVG(rating) as new_avg FROM reviews WHERE movie_id = $movie_id";
        $avg_result = mysqli_query($conn, $avg_sql);
        $avg_data = mysqli_fetch_assoc($avg_result);
        $new_rating = number_format((float)$avg_data['new_avg'], 1, '.', '');
        
        // Update Movies Table
        $update_sql = "UPDATE movies SET average_rating = $new_rating WHERE movie_id = $movie_id";
        mysqli_query($conn, $update_sql);
        
        // Get username and avatar for the response
        $user_sql = "SELECT username, avatar_url FROM users WHERE user_id = $user_id";
        $user_result = mysqli_query($conn, $user_sql);
        $user_data = mysqli_fetch_assoc($user_result);
        
        echo json_encode([
            'success' => true,
            'message' => 'Review posted successfully!',
            'new_average_rating' => $new_rating,
            'review_id' => mysqli_insert_id($conn),
            'username' => $user_data['username'],
            'avatar_url' => $user_data['avatar_url'],
            'rating' => $rating,
            'comment' => $comment,
            'date' => date('F j, Y')
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($conn)]);
    }
    exit;
}

// 1. Check if ID exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$movie_id = intval($_GET['id']);
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// 2. Handle New Review Submission
$msg = "";
if (isset($_POST['submit_review'])) {
    if ($user_id == 0) {
        $msg = "Please login to write a review.";
    } else {
        $rating = floatval($_POST['rating']);
        $comment = mysqli_real_escape_string($conn, $_POST['comment']);
        
        // A. Insert the new review
    $sql_review = "INSERT INTO reviews (user_id, movie_id, rating, comment) VALUES ($user_id, $movie_id, $rating, '$comment')";
        
        if (mysqli_query($conn, $sql_review)) {
            $msg = "Review submitted successfully!";
            
            // B. Calculate average
            $avg_sql = "SELECT AVG(rating) as new_avg FROM reviews WHERE movie_id = $movie_id";
            $avg_result = mysqli_query($conn, $avg_sql);
            $avg_data = mysqli_fetch_assoc($avg_result);
            
            // C. Round logic
            $new_rating = number_format((float)$avg_data['new_avg'], 1, '.', '');

            // D. Update Movies Table 
            $update_sql = "UPDATE movies SET average_rating = $new_rating WHERE movie_id = $movie_id";
            
            if (mysqli_query($conn, $update_sql)) {
                // Success! Refresh page to see new rating
                header("Location: movie_info.php?id=$movie_id");
                exit;
            } else {
                //  Print the error 
                echo "<div style='background:red; color:white; padding:20px; font-size:18px;'>";
                echo "<h3>CRITICAL ERROR: Could not update movie rating!</h3>";
                echo "Calculated Rating: <strong>$new_rating</strong><br>";
                echo "MySQL Error Message: " . mysqli_error($conn);
                echo "</div>";
                exit; // Stop everything so you can read the error
            }

        } else {
            $msg = "Error submitting review: " . mysqli_error($conn);
        }
    }
}

// 3. Fetch Movie Details
$sql = "SELECT m.*, GROUP_CONCAT(c.name SEPARATOR ', ') as genres 
        FROM movies m
        LEFT JOIN movie_categories mc ON m.movie_id = mc.movie_id
        LEFT JOIN categories c ON mc.category_id = c.category_id
        WHERE m.movie_id = $movie_id
        GROUP BY m.movie_id";

$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    echo "Movie not found.";
    exit;
}
$movie = mysqli_fetch_assoc($result);

// Check if movie is in user's collection
$in_collection = false;
if ($user_id > 0) {
    $collection_check_sql = "SELECT * FROM collections WHERE user_id = $user_id AND movie_id = $movie_id";
    $collection_check_result = mysqli_query($conn, $collection_check_sql);
    if ($collection_check_result && mysqli_num_rows($collection_check_result) > 0) {
        $in_collection = true;
    }
}

// Enhance movie data with TMDB information
if (!empty($movie['tmdb_id'])) {
    $tmdbData = fetchTMDBData($movie['tmdb_id']);
    
    // Always use TMDB description if available
    if (!empty($tmdbData['overview'])) {
        $movie['description'] = $tmdbData['overview'];
    }
    
    // Get TMDB media URLs (poster and backdrop)
    $mediaUrls = getTMDBMediaUrls($movie['tmdb_id']);
    $movie['poster_url'] = $mediaUrls['poster_url'];
    $movie['backdrop_url'] = $mediaUrls['backdrop_url'];
    
    // Get gallery images from TMDB
    $gallery_images = getTMDBGalleryImages($movie['tmdb_id']);
} else {
    $gallery_images = [];
}

// 4. Fetch Reviews
$sql_reviews = "SELECT r.*, u.username, u.avatar_url 
                FROM reviews r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.movie_id = $movie_id 
                ORDER BY r.created_at DESC";
$reviews_result = mysqli_query($conn, $sql_reviews);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $movie['title']; ?> - Details</title>
    <link rel="stylesheet" href="final.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
    
<body> 
    <?php include 'header.php'; ?>

    <div class="movie-hero">
        <div class="hero-bg" style="background-image: url('<?php echo $movie['backdrop_url']; ?>');"></div>
        <div class="movie-content-wrapper">
            <img src="<?php echo $movie['poster_url']; ?>" alt="Poster" class="info-poster">
            <div class="info-details">
                <h1 class="info-title"><?php echo $movie['title']; ?></h1>
                <div class="info-meta">
                    <span><?php echo $movie['release_year']; ?></span>
                    <i class="ri-checkbox-blank-circle-fill" style="font-size: 6px; opacity: 0.5;"></i>
                    <span><?php echo $movie['genres']; ?></span>
                </div>
                <div class="action-buttons">
                    <div class="rating-circle">
                        <?php echo $movie['average_rating']; ?>
                    </div>
                    <?php if ($in_collection): ?>
                        <button class="action-btn btn-add bookmark bookmarked" data-movie-id="<?php echo $movie['movie_id']; ?>" style="background: var(--live-indicator);">
                            <i class="ri-bookmark-fill"></i> <span class="btn-text">Remove from Collection</span>
                        </button>
                    <?php else: ?>
                        <button class="action-btn btn-add bookmark" data-movie-id="<?php echo $movie['movie_id']; ?>">
                            <i class="ri-bookmark-line"></i> <span class="btn-text">Add to Collection</span>
                        </button>
                    <?php endif; ?>
                </div>
                <br>
                <h3>Description</h3>
                <p class="info-desc"><?php echo $movie['description']; ?></p>
            </div>
        </div>
    </div>

    <!-- Movie Gallery Grid -->
    <?php if (!empty($gallery_images)): ?>
    <div class="movie-gallery-section">
        <div class="gallery-container">
            <h2 class="section-heading">Gallery</h2>
            <div class="gallery-grid">
                <?php 
                $count = 0;
                foreach ($gallery_images as $image): 
                    if ($count >= 9) break;
                ?>
                    <div class="gallery-item">
                        <img src="<?php echo $image; ?>" alt="Movie Image" loading="lazy">
                        <div class="gallery-overlay"></div>
                    </div>
                <?php 
                    $count++;
                endforeach;
                ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="reviews-section">
        <h2 class="section-heading">User Reviews</h2>
        <div class="review-form-container">
            <?php if($msg): ?>
                <p style="color: var(--light-azure); margin-bottom: 10px; font-weight: bold;"><?php echo $msg; ?></p>
            <?php endif; ?>

            <?php if($user_id > 0): ?>
                <form id="review-form" data-movie-id="<?php echo $movie_id; ?>">
                    <h3 style="margin-bottom: 15px; color:white;">Leave a comment</h3>
                    <div style="margin-bottom: 15px;">
                        <label style="color: #ccc; margin-right: 10px;">Rating:</label>
                        <select name="rating" id="review-rating" class="rating-select">
                            <option value="10">10 - Masterpiece</option>
                            <option value="9">9 - Great</option>
                            <option value="8">8 - Good</option>
                            <option value="7">7 - Decent</option>
                            <option value="6">6 - Okay</option>
                            <option value="5">5 - Average</option>
                            <option value="4">4 - Below Average</option>
                            <option value="3">3 - Bad</option>
                            <option value="2">2 - Terrible</option>
                            <option value="1">1 - Worst</option>
                        </select>
                    </div>
                    <textarea name="comment" id="review-comment" class="review-input" rows="4" placeholder="What did you think about this movie?" required></textarea>
                    <button type="submit" class="load-more-btn" style="margin:0;">Post Review</button>
                </form>
            <?php else: ?>
                <p style="color: #ccc;">Please <a href="login.php" style="color: var(--yellow);">Login</a> to write a review.</p>
            <?php endif; ?>
        </div>

        <div class="reviews-list">
            <?php 
            if (mysqli_num_rows($reviews_result) > 0) {
                while($review = mysqli_fetch_assoc($reviews_result)): 
            ?>
                <div class="review-item" data-review-id="<?php echo $review['review_id']; ?>">
                    <div class="review-avatar">
                        <?php if(!empty($review['avatar_url'])): ?>
                            <img src="<?php echo htmlspecialchars($review['avatar_url']); ?>" alt="<?php echo htmlspecialchars($review['username']); ?>">
                        <?php else: ?>
                            <?php echo strtoupper(substr($review['username'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <div class="review-content">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h4><?php echo htmlspecialchars($review['username']); ?></h4>
                            <?php if($user_id == $review['user_id']): ?>
                                <div class="review-actions">
                                    <button data-action="edit">
                                        <i class="ri-edit-line"></i> Edit
                                    </button>
                                    <button data-action="delete">
                                        <i class="ri-delete-bin-line"></i> Delete
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="stars">
                            <i class="ri-star-fill"></i> <span class="review-rating"><?php echo $review['rating']; ?></span> / 10
                        </div>
                        
                        <!-- Display mode -->
                        <div class="review-display">
                            <p class="review-text"><?php echo htmlspecialchars($review['comment']); ?></p>
                        </div>
                        
                        <!-- Edit mode (hidden by default) -->
                        <div class="review-edit-form" style="display: none;">
                            <div style="margin-bottom: 10px;">
                                <label style="color: #aaa; font-size: 12px;">Rating:</label>
                                <select class="edit-rating" style="background: var(--rich-black-fogra-29); color: white; padding: 8px; border-radius: 5px; border: 1px solid rgba(255,255,255,0.1); margin-left: 10px;">
                                    <?php for($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ($review['rating'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <textarea class="edit-comment" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 10px; border-radius: 8px; min-height: 80px; resize: vertical; font-family: inherit;"><?php echo htmlspecialchars($review['comment']); ?></textarea>
                            <div style="margin-top: 10px; display: flex; gap: 10px;">
                                <button type="button" class="save-edit-btn" style="padding: 8px 20px; background: var(--light-azure); color: white; border: none; border-radius: 5px; cursor: pointer;">Save</button>
                                <button type="button" class="cancel-edit-btn" style="padding: 8px 20px; background: rgba(255,255,255,0.1); color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                            </div>
                        </div>
                        
                        <div class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></div>
                    </div>
                </div>
            <?php 
                endwhile;
            } else {
                echo "<p style='color: #888;'>No reviews yet. Be the first to review!</p>";
            }
            ?>
        </div>
    </div>
    
    <script src="final.js"></script>

    <footer>
        Images are screenshots from "<?php echo $movie['title']; ?>" (<?php echo $movie['release_year']; ?>).
        <br>
        Which are taken from TMDB. Used strictly for educational purposes.
    </footer>

</body>
</html>