<?php
// 1. Connection Configurations
$host = "localhost";
$port = "5432";
$dbname = "local_service_finder"; // The exact name of the database you just created
$user = "postgres";
$password = "supto00000"; // ⚠️ Replace this with your database password

// 2. Build the connection string
$connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password}";

// 3. Attempt to establish connection
$db_conn = pg_connect($connection_string);

// 4. Check if the connection worked
if (!$db_conn) {
    die("Database Connection Error: " . pg_last_error());
}

// You can uncomment the line below to test it in the browser:
// echo "Success! Connected to the database.";
?>