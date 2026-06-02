<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Provider | Local Service Finder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/user-messages-style.css">
</head>
<body class="dashboard-body">

    <aside class="sidebar">
        <div class="sidebar-logo">LS<span>Finder</span></div>
        <ul class="sidebar-menu">
            <li><a href="user-dashboard.php?view=bookings"><i class="fas fa-home"></i> <span>My Bookings</span></a></li>
            <li><a href="search.php"><i class="fas fa-search"></i> <span>Find Services</span></a></li>
            <li><a href="user-dashboard.php?view=favorites"><i class="fas fa-heart"></i> <span>Saved Providers</span></a></li>
            <li class="active"><a href="user-messages.php"><i class="fas fa-comments"></i> <span>Messages</span></a></li>
        </ul>
        <ul class="sidebar-menu logout-section">
            <li class="logout-link"><a href="logout.php"><i class="fas fa-power-off"></i> <span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="dashboard-main">
        <div class="chat-container user-chat-main-box" style="display: grid; grid-template-columns: 320px 1fr; height: calc(100vh - 120px); min-height: 550px;">
            
            <div class="user-inbox-sidebar-panel" style="border-right: 1px solid #eee; display: flex; flex-direction: column; background: #fafafa;">
                <div style="padding: 24px 20px; border-bottom: 1px solid #eee; background: white;">
                    <h3 style="color: #1a2a6c; font-weight: 800; font-size: 18px; margin: 0;"><i class="fas fa-comments" style="margin-right: 8px; color: #1a2a6c;"></i>My Chats</h3>
                </div>
                
                <div class="inbox-scroll-list" style="overflow-y: auto; flex: 1;">
                    <?php if (!empty($active_chats)): ?>
                        <?php foreach ($active_chats as $chat_item): 
                            $is_current_pro = ($chat_item['partner_id'] === $provider_id);
                            $bg_item_color = $is_current_pro ? '#f0f4f8' : 'transparent';
                            $border_left_accent = $is_current_pro ? '4px solid #1a2a6c' : '4px solid transparent';
                        ?>
                            <a href="user-messages.php?provider_id=<?php echo $chat_item['partner_id']; ?>" style="display: flex; align-items: center; gap: 12px; padding: 15px 18px; text-decoration: none; border-bottom: 1px solid #f2f2f2; background: <?php echo $bg_item_color; ?>; border-left: <?php echo $border_left_accent; ?>; transition: all 0.2s;">
                                <img src="<?php echo $chat_item['avatar_render_path']; ?>?v=<?php echo time(); ?>" alt="Provider Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 3px;">
                                        <h5 style="margin: 0; font-weight: 700; color: #1a2a6c; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($chat_item['partner_name']); ?></h5>
                                        <span style="font-size: 10px; color: #999;"><?php echo $chat_item['formatted_time']; ?></span>
                                    </div>
                                    <p style="margin: 0; font-size: 11px; color: #28a745; font-weight: 600; margin-bottom: 2px;"><?php echo htmlspecialchars($chat_item['specialty']); ?></p>
                                    <p style="margin: 0; font-size: 12px; color: #777; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($chat_item['snippet']); ?>
                                    </p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; color: #aaa; padding: 40px 20px; font-size: 13px;">
                            <i class="fas fa-folder-open" style="font-size: 24px; display: block; margin-bottom: 8px; color: #ccc;"></i> No message records found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="user-active-conversation-pane" style="display: flex; flex-direction: column; background: white;">
                
                <div class="chat-header user-chat-top-header" style="background: white;">
                    <div class="chat-user-info user-chat-meta-row">
                        <img src="<?php echo $active_provider_avatar; ?>?v=<?php echo time(); ?>" alt="Provider" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        <div>
                            <h4><?php echo $provider_name; ?> (Provider)</h4>
                            <?php if (!empty($message_thread)): ?>
                                <span class="status-dot user-chat-online-badge"><i class="fas fa-circle"></i>Active Thread Channel</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="message-display user-chat-display-scroller" id="chatWindow" style="flex: 1; height: auto;">
                    <?php if (!empty($message_thread)): ?>
                        <?php foreach ($message_thread as $msg): ?>
                            <div class="msg <?php echo $msg['class']; ?> <?php echo $msg['alignment_variant_class']; ?>">
                                <p><?php echo htmlspecialchars($msg['message_text']); ?></p>
                                <span class="msg-time" style="display: block; font-size: 10px; text-align: right; margin-top: 5px; color: <?php echo $msg['time_color']; ?>;"><?php echo $msg['time_stamp']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style='text-align:center; color:#bbb; margin-top:150px; font-size:14px;'>
                            <i class='fas fa-comments' style='font-size:30px; display:block; margin-bottom:10px;'></i>
                            Select an expert service pro from the left panel column list to open your message history log thread.
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($provider_id !== null): ?>
                    <form action="user-messages.php?provider_id=<?php echo $provider_id; ?>" method="POST" class="chat-input-area user-chat-input-form-tray" style="background: white; border-top: 1px solid #eee;">
                        <div class="input-wrapper">
                            <input type="text" name="message_text" required placeholder="Write your message to the provider..." autocomplete="off">
                        </div>
                        <button type="submit" name="send_message" class="user-chat-btn-send">
                            Send <i class="fas fa-paper-plane" style="margin-left: 5px;"></i>
                        </button>
                    </form>
                <?php endif; ?>
                
            </div>
        </div>
    </main>

    <script src="js/user-messages.js"></script>
</body>
</html>