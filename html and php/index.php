<?php 

session_start();


include "config.php";
include "tmdb_helper.php";




// A. get banner data
$banner_sql = "SELECT m.*, GROUP_CONCAT(c.name SEPARATOR '/') as movie_categories 
               FROM movies m
               LEFT JOIN movie_categories mc ON m.movie_id = mc.movie_id
               LEFT JOIN categories c ON mc.category_id = c.category_id
               GROUP BY m.movie_id
               ORDER BY m.release_year DESC 
               LIMIT 4";
$banner_result = $conn->query($banner_sql);
$banner_movies = []; 
if ($banner_result->num_rows > 0) {
    while($row = $banner_result->fetch_assoc()) {
        // Enhance with TMDB data
        if (!empty($row['tmdb_id'])) {
            $tmdbData = fetchTMDBData($row['tmdb_id']);
            if (!empty($tmdbData['overview'])) {
                $row['description'] = $tmdbData['overview'];
            }
        }
        $banner_movies[] = $row;
    }
}

// B. get movie (Grid) - Default: Popular movies (rating >= 7.5)
$grid_sql = "SELECT m.*, GROUP_CONCAT(c.name SEPARATOR '/') as genres 
             FROM movies m
             LEFT JOIN movie_categories mc ON m.movie_id = mc.movie_id
             LEFT JOIN categories c ON mc.category_id = c.category_id
             WHERE m.average_rating >= 7.5
             GROUP BY m.movie_id
             ORDER BY m.average_rating DESC, m.movie_id DESC
             LIMIT 12";
$grid_result = $conn->query($grid_sql);


$cat_sql = "SELECT c.category_id, c.name, 
            (SELECT COUNT(*) FROM movie_categories mc WHERE mc.category_id = c.category_id) as total_movies 
            FROM categories c";
$cat_result = $conn->query($cat_sql);

// Store categories in array for later use
$categories = [];
if ($cat_result && $cat_result->num_rows > 0) {
    while($cat_row = $cat_result->fetch_assoc()) {
        $categories[] = $cat_row;
    }
}

$genre_list_sql = "SELECT category_id, name FROM categories ORDER BY name ASC";
$genre_list_result = $conn->query($genre_list_sql);


$year_list_sql = "SELECT DISTINCT release_year FROM movies ORDER BY release_year DESC";
$year_list_result = $conn->query($year_list_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="final.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body>
    
    <?php include 'header.php'; ?>
    
    <div class="container">
        
        <main>

            <div class="carousel next">
                <div class="list">
                    <?php foreach ($banner_movies as $movie): ?>
                    <div class="item">
                        <img src="<?php echo $movie['backdrop_url']; ?>" class="item-pic" alt="<?php echo $movie['title']; ?>">
                        <div class="content">
                            <div class="genre">
                                <i class="ri-film-line"></i>
                                <span><?php echo $movie['movie_categories']; ?></span>
                            </div>
                            <div class="year">
                                <i class="ri-calendar-line"></i>
                                <span><?php echo $movie['release_year']; ?></span>
                            </div>
                            <div class="rating">
                                <i class="ri-star-line"></i>
                                <span><?php echo $movie['average_rating']; ?></span>
                            </div>
                            <div class="title">
                                <h2><?php echo $movie['title']; ?></h2>
                            </div>
                            <div class="description">
                                <p><?php echo $movie['description']; ?></p> 
                            </div>
                            <div class="buttons">
                                <button onclick="window.location.href='movie_info.php?id=5'" 
        							style="color: #fff; background-color: #0056b3; opacity: 1;">More Info</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="thumbnails"> 
                        <?php foreach ($banner_movies as $movie): ?>
                        <div class="item">
                            <img src="<?php echo $movie['poster_url']; ?>" alt="Poster image of the movie <?php echo $movie['title']; ?>">
                            <div class="content">
                                <div class="thumbnail_title">
                                    <?php echo $movie['title']; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    </div>
            </div>


            <section class="movie">
               <div class="filter-bar">
                   
                    <fieldset class="filter-radios">
                        <legend class="visually-hidden">Sort movies by</legend>
                        <input type="radio" name="garde" id="popular" checked>
                        <label for="popular">Popular</label>
                        <input type="radio" name="garde" id="newest">
                        <label for="newest">Newest</label>
                        <div class="checked-radio-bg"></div>
                    </fieldset>
                </div>


                <div class="movie-grid">
                    <?php 
                    if ($grid_result->num_rows > 0):
                        while($movie = $grid_result->fetch_assoc()): 
                    ?>
                    <div class="movie-card">
                        <div class="card-head">
                            <img src="<?php echo $movie['poster_url']; ?>" alt="Movie poster for <?php echo $movie['title']; ?>" class="card-img"> <!-- MOVIE PICTURE -->
                            <div class="card-overlay">
                               <div class="bookmark" data-movie-id="<?php echo $movie['movie_id']; ?>" role="button" aria-label="Add to collection" tabindex="0">
                                    <i class="ri-bookmark-line" aria-hidden="true"></i>
                                </div>
                                <div class="rating">
                                    <i class="ri-star-line"></i>
                                    <span><?php echo $movie['average_rating']; ?></span>
                                </div>
                                <div class="more-info">
                                    <a href="movie_info.php?id=<?php echo $movie['movie_id']; ?>" style="text-decoration: none; color: white;" aria-label="More info about <?php echo htmlspecialchars($movie['title']); ?>">
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
                        echo "<p style='color:white; text-align:center;'>No movies found.</p>";
                    endif;
                    ?>
                </div>

                <button class="load-more-btn" fdprocessedid="4z7ybi" disabled
        			style="color: #fff; background-color: #0056b3; opacity: 0.7; cursor: not-allowed;">No More Movies</button>

            </section>

            <section class="category" id="category">
                <h2 class="section-heading">Category</h2>
                <div class="category-grid">
                    <?php 
                    if (count($categories) > 0) {
                        foreach($categories as $cat) {
                            $name_lower = strtolower($cat['name']);
                            $img_path = "img/" . $name_lower . ".jpg";
                            
                            if (!file_exists($img_path)) {
                                $img_path = "img/" . $name_lower . ".webp";
                            }
                            if (!file_exists($img_path)) {
                                $img_path = "img/" . strtoupper($cat['name']) . ".jpg";
                            }
                            if (!file_exists($img_path)) {
                                $img_path = "img/default.jpg"; 
                            }
                            
                            echo '<a href="category.php?id=' . $cat['category_id'] . '" class="category-card">';
                            echo '<img src="' . $img_path . '" alt="' . htmlspecialchars($cat['name']) . '" class="card-img">';
                            echo '<div class="name">' . htmlspecialchars($cat['name']) . '</div>';
                            echo '<div class="total">' . intval($cat['total_movies']) . '</div>';
                            echo '</a>';
                        }
                    }
                    ?>
                </div>
            </section>

        </main>
    </div>
    <script src="final.js"></script>
</body>
</html>