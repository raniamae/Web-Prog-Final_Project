<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Testing database connection...<br>";

include "config.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "✅ Connected to database successfully<br><br>";

// Check if tables exist
$tables = ['users', 'movies', 'reviews', 'collections', 'categories', 'movie_categories'];

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✅ Table '$table' exists<br>";
    } else {
        echo "❌ Table '$table' NOT FOUND<br>";
    }
}

echo "<br>Database check complete!";
$conn->close();
?>
