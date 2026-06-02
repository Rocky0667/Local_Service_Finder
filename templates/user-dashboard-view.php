<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | Local Service Finder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/user-dashboard-style.css">
</head>
<body class="dashboard-body">

    <aside class="sidebar">
        <div class="sidebar-logo">LS<span>Finder</span></div>
        <ul class="sidebar-menu">
            <li class="<?php echo ($view_state === 'bookings') ? 'active' : ''; ?>">
                <a href="user-dashboard.php?view=bookings"><i class="fas fa-home"></i> <span>My Bookings</span></a>
            </li>
            <li>
                <a href="search.php"><i class="fas fa-search"></i> <span>Find Services</span></a>
            </li>
            <li class="<?php echo ($view_state === 'favorites') ? 'active' : ''; ?>">
                <a href="user-dashboard.php?view=favorites"><i class="fas fa-heart"></i> <span>Saved Providers</span></a>
            </li>
            <li>
                <a href="#" onclick="openUserModal()"><i class="fas fa-user-circle"></i> <span>My Account</span></a>
            </li>
            <li>
                <a href="index.php"><i class="fas fa-globe"></i> <span>Go to Homepage</span></a>
            </li>
        </ul>
        <ul class="sidebar-menu logout-section">
            <li class="logout-link"><a href="logout.php"><i class="fas fa-power-off"></i> <span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="dashboard-main">
        <header class="dash-header" style="position: relative;">
            <div class="dash-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search your booking history...">
            </div>
            
            <div class="dash-user-nav">
                <div class="notification-bell" onclick="toggleNotifDropdown()" style="cursor: pointer;">
                    <i class="fas fa-bell"></i>
                    <span class="bell-badge"><?php echo $notif_count; ?></span>
                </div>
                
                <div class="notif-dropdown" id="notifBox">
                    <div style="background: #1a2a6c; color: white; padding: 12px 18px; font-weight: 700; font-size: 14px;">Live Account Notifications</div>
                    <?php if (!empty($account_alerts)): ?>
                        <?php foreach ($account_alerts as $alert): ?>
                            <div class='notif-item'>Your service request to <strong><?php echo $alert['provider_name']; ?></strong> was <strong><?php echo $alert['label']; ?></strong>.</div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class='notif-item' style='text-align:center; color:#999; padding:20px;'>No update logs found.</div>
                    <?php endif; ?>
                </div>

                <div class="nav-divider"></div>
                <div class="user-profile-small" onclick="openUserModal()" style="cursor: pointer;">
                    <div class="user-info-text">
                        <p class="user-name"><?php echo htmlspecialchars($customer_name); ?></p>
                        <span class="user-status" style="color: #1a2a6c;">CLIENT</span>
                    </div>
                    <img src="<?php echo $customer_avatar_path; ?>?v=<?php echo time(); ?>" alt="Profile" style="width:45px; height:45px; border-radius:50%; object-fit:cover;">
                </div>
            </div>
        </header>

        <?php if (empty($customer_phone)): ?>
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; font-weight: 600; font-size: 14px;" class="slide-in-left">
                <span><i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i> Warning: You must add a phone contact number inside your profile account settings before booking any service appointments.</span>
                <button onclick="openUserModal()" style="background: #721c24; color: white; border: none; padding: 8px 15px; border-radius: 8px; font-weight: 700; cursor: pointer;">Add Number Now</button>
            </div>
        <?php endif; ?>

        <?php if ($view_state === 'bookings'): ?>
            <div style="margin-bottom: 30px;">
                <h2 style="font-weight: 800; color: #1a2a6c; font-size: 28px;">My Service Bookings</h2>
            </div>

            <section class="booking-tabs slide-in-left">
                <a href="user-dashboard.php?tab=All&view=bookings" class="tab-btn <?php echo ($tab_filter === 'All') ? 'active-tab' : ''; ?>" style="padding: 12px 25px; border-radius: 10px; margin-right: 10px; font-weight:700;">All Bookings</a>
                <a href="user-dashboard.php?tab=Pending&view=bookings" class="tab-btn <?php echo ($tab_filter === 'Pending') ? 'active-tab' : ''; ?>" style="padding: 12px 25px; border-radius: 10px; margin-right: 10px; font-weight:700;">Pending</a>
                <a href="user-dashboard.php?tab=Completed&view=bookings" class="tab-btn <?php echo ($tab_filter === 'Completed') ? 'active-tab' : ''; ?>" style="padding: 12px 25px; border-radius: 10px; font-weight:700;">Completed</a>
            </section>

            <section class="user-bookings-list slide-in-left" style="animation-delay: 0.1s; margin-top: 25px;">
                <?php if (!empty($bookings_list)): ?>
                    <?php foreach ($bookings_list as $row): ?>
                        <div class="booking-item-card">
                            <div class="booking-provider-info">
                                <img src="<?php echo $row['provider_avatar_path']; ?>?v=<?php echo time(); ?>" alt="Avatar" style="width:80px; height:80px; border-radius:15px; object-fit:cover;">
                                <div>
                                    <h4><?php echo htmlspecialchars($row['provider_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($row['specialty']); ?></p>
                                </div>
                            </div>
                            
                            <div class="booking-date">
                                <p><strong>Date:</strong> <?php echo $row['formatted_date']; ?></p>
                                <p><strong>Time:</strong> <?php echo $row['formatted_time']; ?></p>
                            </div>
                            
                            <div class="booking-status">
                                <span class="<?php echo $row['computed_status_class']; ?>"><?php echo $row['computed_status_text']; ?></span>
                            </div>
                            
                            <div class="booking-actions" style="display: flex; flex-direction: column; gap: 8px; align-items: flex-end;">
                                <a href="user-messages.php?provider_id=<?php echo $row['provider_id']; ?>" class="review-btn-link" style="background: #1a2a6c; color: white; text-align: center; display: block; text-decoration: none; width: 130px; font-size: 12px; padding: 8px 0; border-radius: 10px; font-weight: 700;">
                                    <i class="fas fa-comments"></i> Message Pro
                                </a>

                                <?php if (strtolower($row['status']) === 'pending'): ?>
                                    <form action="user-dashboard.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request?');" style="margin: 0;">
                                        <input type="hidden" name="cancel_booking_id" value="<?php echo $row['booking_id']; ?>">
                                        <button type="submit" class="cancel-btn" style="border: 2px solid #dc3545; padding: 6px 0; border-radius: 10px; font-weight: 800; font-size: 12px; background:transparent; color:#dc3545; cursor:pointer; width: 130px; text-align: center;">Cancel Request</button>
                                    </form>
                                <?php elseif (strtolower($row['status']) === 'accepted' || strtolower($row['status']) === 'completed'): ?>
                                    <a href="profile.php?id=<?php echo $row['provider_id']; ?>&review_booking_id=<?php echo $row['booking_id']; ?>#review-anchor" class="review-btn-link" style="text-decoration:none; background:#28a745; width: 130px; text-align: center; font-size: 12px; padding: 8px 0; border-radius: 10px; font-weight: 700;">Rate Service</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style='text-align: center; padding: 50px; background: white; border-radius: 20px; border: 1px solid #eee; width: 100%;'>
                        <i class='fas fa-calendar-times' style='font-size: 45px; color: #ccc; margin-bottom: 15px;'></i>
                        <h4>No Bookings Categorized Under <?php echo $tab_filter; ?></h4>
                    </div>
                <?php endif; ?>
            </section>

        <?php elseif ($view_state === 'favorites'): ?>
            <div style="margin-bottom: 30px;">
                <h2 style="font-weight: 800; color: #1a2a6c; font-size: 28px;">Saved Favorite Providers</h2>
                <p style="color: #666; font-size: 14px; margin-top: 5px;">Your pinned expert technicians and professionals list.</p>
            </div>

            <section class="user-bookings-list slide-in-left">
                <?php if (!empty($favorites_list)): ?>
                    <?php foreach ($favorites_list as $fav): ?>
                        <div class="booking-item-card">
                            <div class="booking-provider-info">
                                <img src="<?php echo $fav['favorite_avatar_path']; ?>?v=<?php echo time(); ?>" alt="Avatar" style="width:70px; height:70px; border-radius:15px; object-fit:cover;">
                                <div>
                                    <h4><?php echo htmlspecialchars($fav['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($fav['specialty']); ?></p>
                                </div>
                            </div>
                            <div class="booking-date">
                                <p><strong>Specialty Area:</strong></p>
                                <p style="color: #fdbb2d; font-weight:700;"><i class="fas fa-star"></i> <?php echo number_format($fav['rating'],1); ?> Score</p>
                            </div>
                            <div class="booking-status">
                                <span class="status-active" style="background:#e6f4ea; color:#137333;"><i class="fas fa-heart"></i> Pinned Favorite</span>
                            </div>
                            <div class="booking-actions" style="display:flex; flex-direction:column; gap:8px;">
                                <a href="user-messages.php?provider_id=<?php echo $fav['provider_id']; ?>" class="review-btn-link" style="background: #1a2a6c !important; color: #ffffff !important; width: 130px; text-align: center; padding: 8px 0; border-radius: 10px; font-size: 12px; font-weight: 700; text-decoration: none; display: block;">
                                    <i class="fas fa-comments" style="color: #ffffff !important;"></i> Chat Thread
                                </a>
                                <a href="profile.php?id=<?php echo $fav['provider_id']; ?>" class="review-btn-link" style="background:transparent; border:2px solid #eee; color:#555; width:130px; text-align:center; padding:6px 0; border-radius:10px; font-size:12px; font-weight:700; text-decoration:none;">View Profile</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style='text-align: center; padding: 50px; background: white; border-radius: 20px; border: 1px solid #eee; width: 100%;'>
                        <i class='fas fa-heart-break' style='font-size: 45px; color: #ccc; margin-bottom: 15px;'></i>
                        <h4 style='color: #1a2a6c;'>No Saved Providers Yet</h4>
                        <p style='color:#777; font-size:13px; margin-top:5px;'>You can add favorites directly while browsing provider profile pages.</p>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <div id="userSettingsModal" class="customer-settings-modal-viewport">
        <div class="customer-settings-modal-card">
            <span onclick="closeUserModal()" class="customer-modal-close-trigger">&times;</span>
            <h2>Account Settings</h2>
            <p>Manage your display name, contact phone number and photo details below.</p>
            
            <form action="user-dashboard.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_user_profile" value="1">
                <div class="form-group" style="margin-bottom:15px;">
                    <label style="font-weight:700; margin-bottom:5px; display:block;">Profile Picture</label>
                    <input type="file" name="user_pic" accept="image/*">
                </div>
                <div class="form-group" style="margin-bottom:15px;">
                    <label style="font-weight:700; margin-bottom:5px; display:block;">Display Full Name</label>
                    <input type="text" name="user_name" value="<?php echo htmlspecialchars($customer_name); ?>" required class="customer-modal-input-field">
                </div>
                <div class="form-group" style="margin-bottom:20px;">
                    <label style="font-weight:700; margin-bottom:5px; display:block;">Contact Phone Number</label>
                    <input type="text" name="user_phone" value="<?php echo htmlspecialchars($customer_phone); ?>" placeholder="e.g. +8801XXXXX" required class="customer-modal-input-field">
                </div>
                <button type="submit" class="auth-btn" style="width:100%; padding:14px; border-radius:12px; font-weight:700; cursor:pointer;">Save Account Information</button>
            </form>
        </div>
    </div>

    <script src="js/user-dashboard.js"></script>
</body>
</html>