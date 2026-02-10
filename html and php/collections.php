<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "config.php";


// Get user_id
$user_id = 0;


if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);

} elseif (isset($_SESSION['user_name'])) {
    $user_name = $_SESSION['user_name'];
    $user_sql = "SELECT user_id FROM users WHERE username = '" . mysqli_real_escape_string($conn, $user_name) . "'";
    $user_result = mysqli_query($conn, $user_sql);
    
    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user_row = mysqli_fetch_assoc($user_result);
        $user_id = intval($user_row['user_id']);
        $_SESSION['user_id'] = $user_id;
    } else {
       
        echo "<script>
                alert('User not found. Please login again.');
                window.location.href = 'login.php';
              </script>";
        exit; 
    }

} else {
   
    echo "<script>
            alert('Please login first to view your collection.');
            window.location.href = 'login.php';
          </script>";
    exit; 
}

// Get user's collection
$sql = "SELECT m.*, GROUP_CONCAT(DISTINCT c.name SEPARATOR '/') as genres 
        FROM collections col
        JOIN movies m ON col.movie_id = m.movie_id
        LEFT JOIN movie_categories mc ON m.movie_id = mc.movie_id
        LEFT JOIN categories c ON mc.category_id = c.category_id
        WHERE col.user_id = " . intval($user_id) . "
        GROUP BY m.movie_id
        ORDER BY col.added_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Collection</title>
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
                    <i class="ri-bookmark-fill icon-yellow"></i> My Collection
                </h2>
                </div>

                <div class="movie-grid">
                    <?php 
                    if ($result && mysqli_num_rows($result) > 0):
                        while($movie = mysqli_fetch_assoc($result)): 
                    ?>
                    <div class="movie-card">
                        <div class="card-head">
                            <img src="<?php echo $movie['poster_url']; ?>" alt="<?php echo $movie['title']; ?>" class="card-img">
                            <div class="card-overlay">
                                <div class="bookmark bookmarked" data-movie-id="<?php echo $movie['movie_id']; ?>" role="button" aria-label="Remove from collection" tabindex="0">
                                    <i class="ri-bookmark-fill" aria-hidden="true"></i>
                                </div>
                                <div class="rating">
                                    <i class="ri-star-line" aria-hidden="true"></i>
                                    <span><?php echo $movie['average_rating']; ?></span>
                                </div>
                                <div class="more-info">
                                    <a href="movie_info.php?id=<?php echo $movie['movie_id']; ?>" class="info-link" aria-label="More info about <?php echo htmlspecialchars($movie['title']); ?>">
                                        <i class="ri-information-2-line" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $movie['title']; ?></h3>
                            <div class="card-info">
                                <span class="card-genre"><?php echo $movie['genres'] ? $movie['genres'] : 'General'; ?></span>
                                <span class="card-year"><?php echo $movie['release_year']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    
                    <div class="empty-state">
                        <i class="ri-bookmark-line empty-icon"></i>
                        <p class="empty-title">Your collection is empty</p>
                        <p class="empty-desc">Click the bookmark icon on movies to add them here!</p>
                        <a href="index.php" class="browse-btn">Browse Movies</a>
                    </div>

                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
    <script src="final.js"></script>
</body>
</html>