<?php
// user-messages.php (Pure Backend Logic Controller)
session_start();
require_once 'db_connect.php';

// 1. Guard Check: Ensure user is logged in as a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.html");
    exit;
}

$customer_id = $_SESSION['user_id'];

// 2. FIXED: FETCH ALL UNIQUE PROVIDERS WHOM THIS CUSTOMER HAS MESSAGED (Inbox Sidebar Data Pipeline)
$inbox_query = "SELECT DISTINCT ON (partner_id) 
                    u.id AS partner_id, 
                    u.name AS partner_name, 
                    p.specialty,
                    p.profile_img AS provider_pic,
                    m.message_text AS last_msg, 
                    m.created_at AS last_time
                FROM (
                    SELECT id, sender_id, receiver_id, message_text, created_at,
                           CASE WHEN sender_id = $1 THEN receiver_id ELSE sender_id END AS partner_id
                    FROM messages
                    WHERE sender_id = $1 OR receiver_id = $1
                ) m
                JOIN users u ON m.partner_id = u.id
                -- FIXED: Joined using u.id matching your true relational schema keys
                JOIN provider_details p ON u.id = p.provider_id
                WHERE u.role = 'provider'
                ORDER BY partner_id, m.created_at DESC";
                
$inbox_res = pg_query_params($db_conn, $inbox_query, array($customer_id));
$active_chats = [];

if ($inbox_res && pg_num_rows($inbox_res) > 0) {
    while ($row = pg_fetch_assoc($inbox_res)) {
        $row['formatted_time'] = date("h:i A", strtotime($row['last_time']));
        $row['snippet'] = strlen($row['last_msg']) > 35 ? substr($row['last_msg'], 0, 32) . '...' : $row['last_msg'];
        
        // Resolve dynamic sidebar profile image path checking filesystem accurately
        $row['avatar_render_path'] = (!empty($row['provider_pic']) && file_exists("uploads/" . $row['provider_pic'])) 
            ? "uploads/" . $row['provider_pic'] 
            : "https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=50&q=80";
            
        $active_chats[] = $row;
    }
}

// 3. Determine who the customer is currently chatting with
if (!isset($_GET['provider_id'])) {
    if (!empty($active_chats)) {
        // Automatically default lock onto the first contact inside the active history array list
        $provider_id = intval($active_chats[0]['partner_id']);
    } else {
        // No chat history anywhere yet, route them to find a pro
        header("Location: search.php");
        exit;
    }
} else {
    $provider_id = intval($_GET['provider_id']);
}

// 4. Process Sending Message Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $message_text = trim(htmlspecialchars($_POST['message_text']));
    
    if (!empty($message_text)) {
        $insert_query = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES ($1, $2, $3)";
        pg_query_params($db_conn, $insert_query, array($customer_id, $provider_id, $message_text));
        
        header("Location: user-messages.php?provider_id=" . $provider_id);
        exit;
    }
}

// 5. Fetch Targeted Provider Meta Info and Real-time Picture Path parameters
$provider_name = "Service Provider";
$active_provider_avatar = "https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=50&q=80";

if ($provider_id !== null) {
    $provider_query = "SELECT u.name, p.specialty, p.profile_img FROM users u 
                       JOIN provider_details p ON u.id = p.provider_id 
                       WHERE u.id = $1 AND u.role = 'provider'";
    $provider_res = pg_query_params($db_conn, $provider_query, array($provider_id));
    
    if ($provider_res && pg_num_rows($provider_res) > 0) {
        $provider_meta = pg_fetch_assoc($provider_res);
        $provider_name = htmlspecialchars($provider_meta['name']);
        
        if (!empty($provider_meta['profile_img']) && file_exists("uploads/" . $provider_meta['profile_img'])) {
            $active_provider_avatar = "uploads/" . $provider_meta['profile_img'];
        }
    }
}

// 6. Query active chronological message stream history records
$message_thread = [];
$chat_query = "SELECT * FROM messages 
               WHERE (sender_id = $1 AND receiver_id = $2) 
                  OR (sender_id = $2 AND receiver_id = $1) 
               ORDER BY created_at ASC";
$chat_res = pg_query_params($db_conn, $chat_query, array($customer_id, $provider_id));

if ($chat_res && pg_num_rows($chat_res) > 0) {
    while ($msg = pg_fetch_assoc($chat_res)) {
        $is_sent = ($msg['sender_id'] == $customer_id);
        
        $msg['class'] = $is_sent ? 'sent' : 'received';
        $msg['alignment_variant_class'] = $is_sent ? 'user-bubble-sent-client' : 'user-bubble-received-provider';
        $msg['time_stamp'] = date("h:i A", strtotime($msg['created_at']));
        $msg['time_color'] = $is_sent ? 'rgba(255,255,255,0.7)' : '#888';
        
        $message_thread[] = $msg;
    }
}

// 7. RENDER THE TEMPLATE VIEW
include 'templates/user-messages-view.php';