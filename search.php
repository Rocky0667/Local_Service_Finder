<?php
// search.php (Pure Backend Logic Controller)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

// 1. DATA PARSING: Capture filter inputs securely
$search_service  = isset($_GET['service']) ? trim($_GET['service']) : '';
$search_location = isset($_GET['location']) ? trim($_GET['location']) : '';

// 2. QUERY BUILDING FRAMEWORK: Handle conditional statements parameter maps dynamically
$query = "SELECT u.id, u.name, u.email, p.specialty, p.service_area, p.rating, p.profile_img 
          FROM users u 
          JOIN provider_details p ON u.id = p.provider_id 
          WHERE u.role = 'provider'";

$params = array();
$param_counter = 1;

if (!empty($search_service)) {
    $query .= " AND (p.specialty ILIKE $" . $param_counter . " OR u.name ILIKE $" . $param_counter . ")";
    $params[] = '%' . $search_service . '%';
    $param_counter++;
}

if (!empty($search_location)) {
    $query .= " AND p.service_area ILIKE $" . $param_counter;
    $params[] = $search_location;
    $param_counter++;
}

$query .= " ORDER BY p.rating DESC";
$result = pg_query_params($db_conn, $query, $params);

// 3. STORAGE HYDRATION: Map resources cleanly before loading structural view components
$search_records = [];
if ($result && pg_num_rows($result) > 0) {
    while ($row = pg_fetch_assoc($result)) {
        $avatar = (!empty($row['profile_img']) && file_exists("./uploads/" . $row['profile_img'])) 
            ? "./uploads/" . $row['profile_img'] 
            : "https://images.unsplash.com/photo-1540569014015-19a7be504e3a?auto=format&fit=crop&w=400&q=80";
        
        $row['avatar_render_path'] = $avatar;
        $search_records[] = $row;
    }
}

// 4. LOAD PRESENTATION LAYER
include 'templates/search-view.php';