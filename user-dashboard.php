<?php
// user-dashboard.php (Pure Backend Logic Controller)
session_start();
require_once 'db_connect.php';

// 1. Guard Check: Ensure user is logged in as a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.html");
    exit;
}

$customer_id = $_SESSION['user_id'];

// 2. PROCESS PROFILE UPDATES (Name, Phone, Profile Image)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user_profile'])) {
    $new_name = trim(htmlspecialchars($_POST['user_name']));
    $new_phone = trim(htmlspecialchars($_POST['user_phone']));
    
    $curr_q = "SELECT phone, profile_img FROM users WHERE id = $1";
    $curr_res = pg_query_params($db_conn, $curr_q, array($customer_id));
    $curr_row = ($curr_res) ? pg_fetch_assoc($curr_res) : null;
    $filename = (!empty($curr_row['profile_img'])) ? $curr_row['profile_img'] : 'default-avatar.png';

    if (isset($_FILES['user_pic']) && $_FILES['user_pic']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['user_pic']['tmp_name'];
        $file_name = $_FILES['user_pic']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($file_ext, $allowed)) {
            $new_filename = "customer_" . $customer_id . "_" . time() . "." . $file_ext;
            
            if (!is_dir('./uploads')) {
                mkdir('./uploads', 0777, true);
            }
            
            if (move_uploaded_file($file_tmp, './uploads/' . $new_filename)) {
                $filename = $new_filename;
            }
        }
    }

    $update_user = "UPDATE users SET name = $1, phone = $2, profile_img = $3 WHERE id = $4";
    $update_res = @pg_query_params($db_conn, $update_user, array($new_name, $new_phone, $filename, $customer_id));
    
    if (!$update_res) {
        $update_user_fallback = "UPDATE users SET name = $1, phone = $2, avatar = $3 WHERE id = $4";
        @pg_query_params($db_conn, $update_user_fallback, array($new_name, $new_phone, $filename, $customer_id));
    }
    
    $_SESSION['user_name'] = $new_name; 
    header("Location: user-dashboard.php");
    exit;
}

// 3. Process Cancellation Request
if (isset($_POST['cancel_booking_id'])) {
    $booking_id_to_cancel = intval($_POST['cancel_booking_id']);
    $cancel_query = "DELETE FROM bookings WHERE id = $1 AND customer_id = $2 AND status = 'Pending'";
    pg_query_params($db_conn, $cancel_query, array($booking_id_to_cancel, $customer_id));
    header("Location: user-dashboard.php");
    exit;
}

// 4. Hydrate Workspace Dashboard Client Account User Meta Data Metrics
$user_q = "SELECT * FROM users WHERE id = $1";
$user_res = pg_query_params($db_conn, $user_q, array($customer_id));

if ($user_res && pg_num_rows($user_res) > 0) {
    $user_profile = pg_fetch_assoc($user_res);
    $customer_name = $user_profile['name'];
    $customer_phone = isset($user_profile['phone']) ? $user_profile['phone'] : "";
    
    $db_avatar_field = "";
    if (!empty($user_profile['profile_img'])) {
        $db_avatar_field = $user_profile['profile_img'];
    } elseif (!empty($user_profile['avatar'])) {
        $db_avatar_field = $user_profile['avatar'];
    }

    $customer_avatar_path = (!empty($db_avatar_field) && file_exists("./uploads/" . $db_avatar_field)) 
        ? "./uploads/" . $db_avatar_field 
        : "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=60&q=80";
} else {
    $customer_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "Client User";
    $customer_phone = "";
    $customer_avatar_path = "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=60&q=80";
}

// 5. Handle Filtering States & Booking Records Iteration Mappings
$tab_filter = isset($_GET['tab']) ? $_GET['tab'] : 'All';
$view_state = isset($_GET['view']) ? $_GET['view'] : 'bookings';

$bookings_list = [];
if ($view_state === 'bookings') {
    $query_str = "SELECT b.id AS booking_id, b.booking_date, b.booking_time, b.status, b.provider_id,
                         u.name AS provider_name, p.specialty, p.profile_img
                  FROM bookings b
                  JOIN users u ON b.provider_id = u.id
                  JOIN provider_details p ON u.id = p.provider_id
                  WHERE b.customer_id = $1";

    if ($tab_filter === 'Pending') {
        $query_str .= " AND b.status = 'Pending'";
    } elseif ($tab_filter === 'Completed') {
        $query_str .= " AND b.status = 'Completed'";
    }
    $query_str .= " ORDER BY b.booking_date DESC, b.booking_time DESC";
    $result = pg_query_params($db_conn, $query_str, array($customer_id));

    if ($result && pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $row['provider_avatar_path'] = (!empty($row['profile_img']) && file_exists("./uploads/" . $row['profile_img'])) 
                ? "./uploads/" . $row['profile_img'] 
                : "https://images.unsplash.com/photo-1540569014015-19a7be504e3a?auto=format&fit=crop&w=80&q=80";
            
            $row['formatted_date'] = date("F d, Y", strtotime($row['booking_date']));
            $row['formatted_time'] = date("h:i A", strtotime($row['booking_time']));
            
            // Pre-compile presentation layer design styles inside logic context
            $status_lower = strtolower($row['status']);
            if ($status_lower === 'accepted') {
                $row['computed_status_class'] = "status-active";
                $row['computed_status_text'] = "Request Approved";
            } elseif ($status_lower === 'declined') {
                $row['computed_status_class'] = "status-declined";
                $row['computed_status_text'] = "Declined";
            } elseif ($status_lower === 'completed') {
                $row['computed_status_class'] = "status-completed";
                $row['computed_status_text'] = "Service Completed";
            } else {
                $row['computed_status_class'] = "status-pending";
                $row['computed_status_text'] = "Pending Approval";
            }
            $bookings_list[] = $row;
        }
    }
}

// 6. Favorites Data Sub-Hydration Pipeline
$favorites_list = [];
if ($view_state === 'favorites') {
    $fav_q = "SELECT f.provider_id, u.name, p.specialty, p.profile_img, p.rating 
              FROM user_favorites f 
              JOIN users u ON f.provider_id = u.id 
              JOIN provider_details p ON u.id = p.provider_id 
              WHERE f.customer_id = $1";
    $fav_res = pg_query_params($db_conn, $fav_q, array($customer_id));

    if ($fav_res && pg_num_rows($fav_res) > 0) {
        while ($fav_row = pg_fetch_assoc($fav_res)) {
            $fav_row['favorite_avatar_path'] = (!empty($fav_row['profile_img']) && file_exists("./uploads/" . $fav_row['profile_img'])) 
                ? "./uploads/" . $fav_row['profile_img'] 
                : "https://images.unsplash.com/photo-1540569014015-19a7be504e3a?auto=format&fit=crop&w=80&q=80";
            $favorites_list[] = $fav_row;
        }
    }
}

// 7. Fetch Unread Interactive Status Notifications Counters
$account_alerts = [];
$notif_q = "SELECT b.id, b.status, u.name as provider_name 
            FROM bookings b 
            JOIN users u ON b.provider_id = u.id 
            WHERE b.customer_id = $1 AND b.status IN ('Accepted', 'Declined', 'Completed') 
            ORDER BY b.id DESC LIMIT 4";
$notif_res = pg_query_params($db_conn, $notif_q, array($customer_id));
$notif_count = ($notif_res) ? pg_num_rows($notif_res) : 0;

if ($notif_count > 0) {
    while ($n_row = pg_fetch_assoc($notif_res)) {
        $st_lower = strtolower($n_row['status']);
        $n_row['label'] = ($st_lower === 'accepted') ? "✅ Approved" : "❌ Declined";
        if ($st_lower === 'completed') $n_row['label'] = "Warmly Completed 🎉";
        $account_alerts[] = $n_row;
    }
}

// 8. COMPILE ENGINE TO OUTPUT FRAME
include 'templates/user-dashboard-view.php';