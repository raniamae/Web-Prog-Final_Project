<?php
$apiKey = "3282c6d622d3ec1a652cd0a33fffafae";

$tmdb_id = $_GET['tmdb_id'] ?? null;

if (!$tmdb_id) {
    die("No movie selected.");
}

// Movie details
$detailsUrl = "https://api.themoviedb.org/3/movie/$tmdb_id?api_key=$apiKey&language=en-US";
$details = json_decode(file_get_contents($detailsUrl), true);

// Images
$imagesUrl = "https://api.themoviedb.org/3/movie/$tmdb_id/images?api_key=$apiKey";
$images = json_decode(file_get_contents($imagesUrl), true);
