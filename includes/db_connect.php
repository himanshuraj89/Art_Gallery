<?php
// Database connection parameters
$host = 'localhost';
$username = 'root';  // Default XAMPP username
$password = '';      // Default XAMPP password (empty)
$database = 'newf';  // Your database name

// First connect without specifying a database
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$create_db_query = "CREATE DATABASE IF NOT EXISTS $database";
if (!$conn->query($create_db_query)) {
    die("Error creating database: " . $conn->error);
}

// Close the initial connection
$conn->close();

// Connect to the specific database
$conn = new mysqli($host, $username, $password, $database);

// Check connection again
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8mb4");

// Echo success message (optional, can be removed in production)
// echo "<script>console.log('Database connection successful!');</script>";
?>
