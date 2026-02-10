<?php
include "config.php";


$offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;

// Show more movies on initial load (12), fewer on "Load More" (6)
$limit = ($offset == 0) ? 12 : 6; 

// filter condition
$genre = isset($_POST['genre']) ? $_POST['genre'] : 'all';
$year = isset($_POST['year']) ? $_POST['year'] : 'all';
$sort = isset($_POST['sort']) ? $_POST['sort'] : 'popular';

// 2. SQL's where condition
$where_clauses = [];

// Popular filter: only show movies with rating >= 7.5
if ($sort == 'popular') {
    $where_clauses[] = "m.average_rating >= 7.5";
}

// Newest filter: Only show 2026 movies
if ($sort == 'newest') {
    $where_clauses[] = "m.release_year = 2026";
}

// Exclude already loaded movie IDs to prevent duplicates
$exclude_ids = isset($_POST['exclude_ids']) ? $_POST['exclude_ids'] : '';
$using_exclude = false;
if (!empty($exclude_ids)) {
    // Sanitize: only allow numbers and commas
    $exclude_ids = preg_replace('/[^0-9,]/', '', $exclude_ids);
    if (!empty($exclude_ids)) {
        $where_clauses[] = "m.movie_id NOT IN ($exclude_ids)";
        $using_exclude = true;
        // When using exclude_ids, don't use offset (exclusion already prevents duplicates)
        $offset = 0;
    }
}

// if user choose Action (genre != 'all'), it will execute this part
if ($genre != 'all') {
    $genre_id = intval($genre);
    // find movie categories ID that is Action」
    $where_clauses[] = "m.movie_id IN (SELECT movie_id FROM movie_categories WHERE category_id = $genre_id)";
}

if ($year != 'all') {
    // Handle year ranges
    if (strpos($year, '-') !== false) {
        $years = explode('-', $year);
        $start_year = intval($years[0]);
        $end_year = intval($years[1]);
        $where_clauses[] = "m.release_year BETWEEN $start_year AND $end_year";
    } else {
        $year_val = intval($year);
        $where_clauses[] = "m.release_year = $year_val";
    }
}

// 組合 WHERE
$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// sequence
$order_sql = "ORDER BY m.average_rating DESC, m.movie_id DESC";
if ($sort == 'newest') {
    $order_sql = "ORDER BY m.average_rating DESC, m.movie_id DESC";
}

// 3. SQL queries
$sql = "SELECT m.*, GROUP_CONCAT(DISTINCT c.name SEPARATOR '/') as genres 
        FROM movies m
        LEFT JOIN movie_categories mc ON m.movie_id = mc.movie_id
        LEFT JOIN categories c ON mc.category_id = c.category_id
        $where_sql
        GROUP BY m.movie_id
        $order_sql
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);


if ($result && $result->num_rows > 0) {
    while($movie = $result->fetch_assoc()) {
        ?>
        <div class="movie-card">
            <div class="card-head">
                <img src="<?php echo $movie['poster_url']; ?>" class="card-img">
                <div class="card-overlay">
                    <div class="bookmark" data-movie-id="<?php echo $movie['movie_id']; ?>"><i class="ri-bookmark-line"></i></div>
                    <div class="rating"><i class="ri-star-line"></i><span><?php echo $movie['average_rating']; ?></span></div>
                    <div class="more-info"><a href="movie_info.php?id=<?php echo $movie['movie_id']; ?>"><i class="ri-information-2-line"></i></a></div>
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
    }
} else {
   
    if ($offset == 0) {
        echo '<p style="color:white; grid-column: 1/-1; text-align: center;">No movies found for this filter.</p>';
    }
}
?>