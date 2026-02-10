<?php
require_once 'config.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

// Search for movies by title
$sql = "SELECT movie_id, title, poster_url, release_year 
        FROM movies 
        WHERE title LIKE '%$query%' 
        ORDER BY title ASC 
        LIMIT 5";

$result = mysqli_query($conn, $sql);

$suggestions = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions[] = [
            'id' => $row['movie_id'],
            'title' => $row['title'],
            'poster' => $row['poster_url'],
            'year' => $row['release_year']
        ];
    }
}

echo json_encode($suggestions);
mysqli_close($conn);
?>