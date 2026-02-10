<?php
session_start();
require_once 'config.php';

// Get search query
$search_query = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

if (empty($search_query)) {
    header('Location: index.php');
    exit;
}

// Search for movies
$sql = "SELECT DISTINCT m.movie_id, m.title, m.poster_url, m.release_year, m.average_rating
        FROM Movies m
        WHERE m.title LIKE '%$search_query%'
        ORDER BY m.title ASC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results: <?php echo htmlspecialchars($search_query); ?> - SineRate</title>
    <link rel="stylesheet" href="final.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body>
    <div class="container">
        <!-- HEADER/NAVBAR -->
        <header>
            <nav class="navbar">
                <a href="index.php" class="navbar-logo">
                    <span>Sine</span>Rate
                </a>

                <button class="navbar-menu-btn">
                    <span class="one"></span>
                    <span class="two"></span>
                    <span class="three"></span>
                </button>

                <nav class="">
                    <ul class="navbar-nav">
                        <li><a href="index.php" class="navbar-link">Home</a></li>
                        
                        <li class="navbar-item dropdown">
                            <a href="javascript:void(0)" class="navbar-link dropdown-btn">
                                Category <i class="ri-arrow-down-s-fill"></i>
                            </a>
                            
                            <div class="dropdown-content">
                                <a href="category.php?id=all">All Movies</a>
                                <?php
                                $cat_sql = "SELECT * FROM categories ORDER BY name ASC";
                                $cat_result = mysqli_query($conn, $cat_sql);
                                if ($cat_result && mysqli_num_rows($cat_result) > 0) {
                                    while ($cat_row = mysqli_fetch_assoc($cat_result)) {
                                        echo '<a href="category.php?id=' . $cat_row['category_id'] . '">' . htmlspecialchars($cat_row['name']) . '</a>';
                                    }
                                }
                                ?>
                            </div>
                        </li>
                        <li><a href="collections.php" class="navbar-link">Collections</a></li>
                    </ul>
                </nav>

                <div class="navbar-actions">
                    <form action="search.php" method="GET" class="navbar-form">
                        <input type="text" class="navbar-form-search" name="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search movies..." required>
                        <button type="submit" class="navbar-form-btn">
                            <i class="ri-search-2-line"></i>
                        </button>
                        <button type="button" class="navbar-form-close">
                            <i class="ri-close-line"></i>
                        </button>
                    </form>
                    <button class="navbar-search-btn">
                        <i class="ri-search-2-line"></i>
                    </button>

                    <!-- User dropdown -->
                    <button class="navbar-user-btn">
                        <i class="ri-user-line"></i>
                        <?php 
                        if (isset($_SESSION['user_id'])) {
                            echo htmlspecialchars($_SESSION['username']);
                        } else {
                            echo 'Account';
                        }
                        ?>
                    </button>

                    <div class="user-box">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <p>Welcome back, <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</p>
                            <a href="profile.php" class="profile_update_btn">Profile</a>
                            <form action="logout.php" method="POST" style="margin: 0;">
                                <button type="submit" class="logout-btn">Logout</button>
                            </form>
                        <?php else: ?>
                            <p>Sign in to save your favorites and write reviews!</p>
                            <a href="login.php" class="login-btn">Login</a>
                            <a href="register.php" class="register-btn">Register</a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </header>

        <!-- SEARCH RESULTS SECTION -->
        <section class="movie search-results">
            <div class="search-header">
                <h1 class="section-heading">
                    Search Results for "<span class="search-term"><?php echo htmlspecialchars($search_query); ?></span>"
                </h1>
                <p class="search-count">
                    <?php 
                    $count = mysqli_num_rows($result);
                    echo $count . ' movie' . ($count != 1 ? 's' : '') . ' found';
                    ?>
                </p>
            </div>

            <?php if ($count > 0): ?>
                <div class="movie-grid">
                    <?php while ($movie = mysqli_fetch_assoc($result)): 
                        // Check if movie is in user's collection
                        $is_bookmarked = false;
                        if (isset($_SESSION['user_id'])) {
                            $user_id = $_SESSION['user_id'];
                            $movie_id = $movie['movie_id'];
                            $bookmark_sql = "SELECT * FROM collections WHERE user_id = $user_id AND movie_id = $movie_id";
                            $bookmark_result = mysqli_query($conn, $bookmark_sql);
                            $is_bookmarked = mysqli_num_rows($bookmark_result) > 0;
                        }
                    ?>
                        <div class="movie-card">
                            <div class="card-head">
                                <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($movie['title']); ?>" 
                                     class="card-img">

                                <div class="card-overlay">
                                    <div class="bookmark <?php echo $is_bookmarked ? 'bookmarked' : ''; ?>" 
                                         data-movie-id="<?php echo $movie['movie_id']; ?>">
                                        <i class="<?php echo $is_bookmarked ? 'ri-bookmark-fill' : 'ri-bookmark-line'; ?>"></i>
                                    </div>

                                    <div class="rating">
                                        <i class="ri-star-fill"></i>
                                        <span><?php echo number_format($movie['average_rating'], 1); ?></span>
                                    </div>

                                    <a href="movie_info.php?id=<?php echo $movie['movie_id']; ?>" class="more-info">
                                        <i class="ri-play-circle-line"></i>
                                    </a>
                                </div>
                            </div>

                            <h3 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <div class="card-info">
                                <span class="card-year"><?php echo $movie['release_year']; ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="ri-search-line empty-icon"></i>
                    <p class="empty-title">No movies found</p>
                    <p class="empty-desc">Try searching with different keywords</p>
                    <a href="index.php" class="browse-btn">Browse All Movies</a>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <script src="final.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>