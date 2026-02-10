<?php
/**
 * TMDB Helper Functions
 * Handles merging local database with TMDB API data
 */

$TMDB_API_KEY = "3282c6d622d3ec1a652cd0a33fffafae";
$TMDB_BASE_URL = "https://api.themoviedb.org/3";

/**
 * Fetch movie details from TMDB API
 * @param int $tmdb_id - The TMDB movie ID
 * @return array - Movie data from TMDB or empty array if error
 */
function fetchTMDBData($tmdb_id) {
    global $TMDB_API_KEY, $TMDB_BASE_URL;
    
    if (!$tmdb_id) return [];
    
    try {
        $url = "$TMDB_BASE_URL/movie/$tmdb_id?api_key=$TMDB_API_KEY&language=en-US";
        $response = @file_get_contents($url);
        
        if ($response === false) return [];
        
        $data = json_decode($response, true);
        return is_array($data) ? $data : [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Merge local movie data with TMDB data
 * Uses local ratings/reviews, supplements with TMDB descriptions if needed
 * @param array $localMovie - Movie data from local database
 * @return array - Merged movie data
 */
function mergeMovieData($localMovie) {
    // If local movie has description, prefer it; otherwise fetch from TMDB
    if (empty($localMovie['description']) && isset($localMovie['tmdb_id'])) {
        $tmdbData = fetchTMDBData($localMovie['tmdb_id']);
        if (!empty($tmdbData['overview'])) {
            $localMovie['description'] = $tmdbData['overview'];
        }
    }
    
    // Ensure critical fields exist
    if (empty($localMovie['poster_url'])) {
        $localMovie['poster_url'] = 'img/default-poster.jpg';
    }
    
    if (empty($localMovie['backdrop_url'])) {
        $localMovie['backdrop_url'] = 'img/default-backdrop.jpg';
    }
    
    return $localMovie;
}

/**
 * Get all movie data with TMDB integration
 * @param string $whereClause - Optional WHERE clause for filtering
 * @param string $orderClause - Optional ORDER clause
 * @param string $limit - Optional LIMIT clause
 * @return array - Array of merged movie data
 */
function getAllMoviesWithTMDB($conn, $whereClause = "", $orderClause = "ORDER BY m.average_rating DESC", $limit = "LIMIT 50") {
    $sql = "SELECT m.*, GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as genres 
            FROM movies m
            LEFT JOIN movie_categories mc ON m.movie_id = mc.movie_id
            LEFT JOIN categories c ON mc.category_id = c.category_id
            $whereClause
            GROUP BY m.movie_id
            $orderClause
            $limit";
    
    $result = $conn->query($sql);
    $movies = [];
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $movies[] = mergeMovieData($row);
        }
    }
    
    return $movies;
}

/**
 * Get single movie with TMDB data
 * @param int $movieId - Local movie ID
 * @return array - Merged movie data or empty array
 */
function getMovieWithTMDB($conn, $movieId) {
    $sql = "SELECT m.*, GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as genres 
            FROM movies m
            LEFT JOIN movie_categories mc ON m.movie_id = mc.movie_id
            LEFT JOIN categories c ON mc.category_id = c.category_id
            WHERE m.movie_id = $movieId
            GROUP BY m.movie_id";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $movie = $result->fetch_assoc();
        return mergeMovieData($movie);
    }
    
    return [];
}

/**
 * Fetch movie images from TMDB API
 * @param int $tmdb_id - The TMDB movie ID
 * @return array - Array of image paths (posters, backdrops)
 */
function fetchTMDBImages($tmdb_id) {
    global $TMDB_API_KEY, $TMDB_BASE_URL;
    
    if (!$tmdb_id) return [];
    
    try {
        $url = "$TMDB_BASE_URL/movie/$tmdb_id/images?api_key=$TMDB_API_KEY";
        $response = @file_get_contents($url);
        
        if ($response === false) return [];
        
        $data = json_decode($response, true);
        return is_array($data) ? $data : [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get poster and backdrop URLs from TMDB
 * @param int $tmdb_id - The TMDB movie ID
 * @return array - Contains 'poster_url' and 'backdrop_url'
 */
function getTMDBMediaUrls($tmdb_id) {
    global $TMDB_BASE_URL;
    
    $tmdbData = fetchTMDBData($tmdb_id);
    $poster_url = 'img/default-poster.jpg';
    $backdrop_url = 'img/default-backdrop.jpg';
    
    if (!empty($tmdbData['poster_path'])) {
        $poster_url = "https://image.tmdb.org/t/p/w342" . $tmdbData['poster_path'];
    }
    
    if (!empty($tmdbData['backdrop_path'])) {
        $backdrop_url = "https://image.tmdb.org/t/p/w1280" . $tmdbData['backdrop_path'];
    }
    
    return [
        'poster_url' => $poster_url,
        'backdrop_url' => $backdrop_url
    ];
}

/**
 * Get gallery images (stills/backdrops) from TMDB - limited to 9
 * @param int $tmdb_id - The TMDB movie ID
 * @return array - Array of image URLs (max 9)
 */
function getTMDBGalleryImages($tmdb_id) {
    $images = fetchTMDBImages($tmdb_id);
    $gallery = [];
    
    // Get backdrops first (usually better quality for gallery)
    if (!empty($images['backdrops'])) {
        foreach (array_slice($images['backdrops'], 0, 9) as $backdrop) {
            if (!empty($backdrop['file_path'])) {
                $gallery[] = "https://image.tmdb.org/t/p/w780" . $backdrop['file_path'];
            }
        }
    }
    
    // If we don't have 9 backdrops, add posters
    if (count($gallery) < 9 && !empty($images['posters'])) {
        foreach (array_slice($images['posters'], 0, 9 - count($gallery)) as $poster) {
            if (!empty($poster['file_path'])) {
                $gallery[] = "https://image.tmdb.org/t/p/w500" . $poster['file_path'];
            }
        }
    }
    
    return $gallery;
}
?>
