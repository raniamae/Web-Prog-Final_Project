<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';

// 2. get ID
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$category_name = "All Movies"; 

// 3. search category
if ($category_id > 0) {
    $cat_name_sql = "SELECT name FROM categories WHERE category_id = $category_id";
    $cat_name_result = mysqli_query($conn, $cat_name_sql);
    
    if ($cat_name_result && mysqli_num_rows($cat_name_result) > 0) {
        $row = mysqli_fetch_assoc($cat_name_result);
        $category_name = $row['name'];
    } else {
        $category_name = "Category Not Found";
    }
}

// 4. 
if ($category_id > 0) {
    $sql = "SELECT m.*, GROUP_CONCAT(c.name SEPARATOR ', ') as genres 
            FROM movies m
            LEFT JOIN movie_categories mc ON m.movie_id = mc.movie_id
            LEFT JOIN categories c ON mc.category_id = c.category_id
            WHERE mc.category_id = $category_id
            GROUP BY m.movie_id
            ORDER BY m.release_year DESC";
} else {
    
    $sql = "SELECT m.*, GROUP_CONCAT(c.name SEPARATOR ', ') as genres 
            FROM movies m
            LEFT JOIN movie_categories mc ON m.movie_id = mc.movie_id
            LEFT JOIN categories c ON mc.category_id = c.category_id
            GROUP BY m.movie_id
            ORDER BY m.release_year DESC";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $category_name; ?> - Movies</title>
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
                        <i class="ri-film-line icon-yellow"></i> <?php echo $category_name; ?>
                    </h2>
                    
                    <div class="filter-inline">
                        <select name="year" id="year">
                                <option value="all">All Years</option>
                                <option value="2026">2026</option>
                                <option value="2024-2025">2024-2025</option>
                                <option value="2022-2023">2022-2023</option>
                                <option value="2020-2021">2020-2021</option>
                                <option value="2010-2019">2010-2019</option>
                                <option value="2000-2009">2000-2009</option>
                                <option value="1980-1999">1980-1999</option>
                            </select>
                    </div>
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
                               <div class="bookmark" data-movie-id="<?php echo $movie['movie_id']; ?>" role="button" aria-label="Add to collection" tabindex="0">
                                    <i class="ri-bookmark-line" aria-hidden="true"></i>
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
                                <span class="card-genre"><?php echo $movie['genres']; ?></span>
                                <span class="card-year"><?php echo $movie['release_year']; ?></span>
                            </div>
                        </div>
                    </div>

                    <?php 
                        endwhile; 
                    else:
                    ?>
                    
                    <div class="empty-state">
                        <i class="ri-movie-2-line empty-icon"></i>
                        <p class="empty-title">No movies found in this category</p>
                        <a href="index.php" class="browse-btn">Go Back Home</a>
                    </div>

                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <script src="final.js"></script>
    
    <script>
    </script>
</body>
</html>