<?php
// index.php (Pure Backend Logic Controller)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

// 1. DATABASE DATA RETRIEVAL: Build top-rated provider memory collection array
$top_query = "SELECT u.id, u.name, p.specialty, p.rating, p.profile_img 
              FROM users u 
              JOIN provider_details p ON u.id = p.provider_id 
              WHERE u.role = 'provider'
              ORDER BY p.rating DESC, u.id ASC 
              LIMIT 3";
$top_result = pg_query($db_conn, $top_query);

$top_providers = [];
if ($top_result && pg_num_rows($top_result) > 0) {
    while ($row = pg_fetch_assoc($top_result)) {
        $p_avatar = (!empty($row['profile_img']) && file_exists("./uploads/" . $row['profile_img'])) 
            ? "./uploads/" . $row['profile_img'] 
            : "https://images.unsplash.com/photo-1540569014015-19a7be504e3a?auto=format&fit=crop&w=400&q=80";
        
        $row['avatar_render_path'] = $p_avatar;
        $top_providers[] = $row;
    }
}

// 2. SESSION VALIDATION PIPELINE: Track user tokens and profile headers dynamically
$is_logged_in = isset($_SESSION['user_id']);
$nav_avatar_path = "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=50&q=80";
$target_dashboard = "#";

if ($is_logged_in) {
    $nav_user_id = $_SESSION['user_id'];
    $nav_role = $_SESSION['user_role'];
    
    if ($nav_role === 'customer') {
        $nav_db_q = "SELECT profile_img FROM users WHERE id = $1";
        $nav_db_res = pg_query_params($db_conn, $nav_db_q, array($nav_user_id));
        if ($nav_db_res && pg_num_rows($nav_db_res) > 0) {
            $nav_row = pg_fetch_assoc($nav_db_res);
            $db_img = !empty($nav_row['profile_img']) ? $nav_row['profile_img'] : '';
            if (!empty($db_img) && file_exists("./uploads/" . $db_img)) {
                $nav_avatar_path = "./uploads/" . $db_img;
            }
        }
        $target_dashboard = "user-dashboard.php";
    } else {
        $nav_db_q = "SELECT profile_img FROM provider_details WHERE provider_id = $1";
        $nav_db_res = pg_query_params($db_conn, $nav_db_q, array($nav_user_id));
        if ($nav_db_res && pg_num_rows($nav_db_res) > 0) {
            $nav_row = pg_fetch_assoc($nav_db_res);
            if (!empty($nav_row['profile_img']) && file_exists("./uploads/" . $nav_row['profile_img'])) {
                $nav_avatar_path = "./uploads/" . $nav_row['profile_img'];
            }
        }
        $target_dashboard = "provider-dashboard.php";
    }
}

// 3. ASSEMBLE RENDER WORKSPACE
include 'templates/index-view.php';