<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($provider['name']); ?> | Profile Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/profile-style.css">
</head>
<body>

    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">LocalService<span>Finder</span></a>
            <ul class="nav-links">
                <li><a href="search.php">Find Services</a></li>
                <li><a href="index.php#about-project">About</a></li>
                <?php
                if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'customer') {
                    echo 'safe_redirect_view';
                    echo '<li><a href="user-dashboard.php" class="active-nav"><i class="fas fa-user-circle"></i> Dashboard</a></li>';
                    echo '<li><a href="logout.php" style="color: #dc3545;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>';
                } else if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'provider') {
                    echo '<li><a href="provider-dashboard.php" class="active-nav"><i class="fas fa-store"></i> Provider Portal</a></li>';
                    echo '<li><a href="logout.php" style="color: #dc3545;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>';
                } else {
                    echo '<li><a href="login.html">Login</a></li>';
                    echo '<li><a href="register.html" class="register-btn">Register</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>

    <section class="profile-banner-custom">
        <div class="profile-banner-container">
            <div class="profile-avatar-group">
                <img src="<?php echo $avatar_path; ?>?v=<?php echo time(); ?>" alt="Provider Photo">
                <div class="profile-meta-text">
                    <h1><?php echo htmlspecialchars($provider['name']); ?></h1>
                    <p><i class="fas fa-tools"></i> <?php echo htmlspecialchars($provider['specialty']); ?></p>
                    <div class="profile-stats-indicator">
                        <span><i class="fas fa-star" style="color: #fdbb2d;"></i> <?php echo number_format($provider['rating'], 1); ?> Rating (<?php echo $total_reviews; ?> Reviews)</span>
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($provider['service_area']); ?>, Bangladesh</span>
                    </div>
                </div>
            </div>

            <?php if ($customer_id): ?>
                <form action="profile.php?id=<?php echo $provider_id; ?>" method="POST">
                    <input type="hidden" name="toggle_favorite" value="1">
                    <button type="submit" class="profile-btn-favorite-toggle" style="background: <?php echo $is_favorited ? '#dc3545' : 'white'; ?>; color: <?php echo $is_favorited ? 'white' : '#dc3545'; ?>;">
                        <i class="fas fa-heart"></i> <?php echo $is_favorited ? 'Saved' : 'Save Provider'; ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <div class="profile-view-layout-grid">
        
        <div class="profile-left-column">
            <main class="profile-overview-card">
                <h3>Professional Overview</h3>
                <h4>"<?php echo htmlspecialchars($provider['tagline']); ?>"</h4>
                <h5>About Me</h5>
                <p><?php echo htmlspecialchars($provider['about']); ?></p>
            </main>

            <?php if (isset($_GET['review_booking_id']) && $customer_id): ?>
                <div id="review-anchor" class="profile-review-submission-box">
                    <h3><i class="fas fa-star" style="color: #fdbb2d;"></i> Rate and Review Service</h3>
                    <p>Share your experience with this professional to finalize your appointment booking.</p>
                    
                    <form action="profile.php?id=<?php echo $provider_id; ?>" method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo intval($_GET['review_booking_id']); ?>">
                        <input type="hidden" name="submit_review" value="1">
                        
                        <div class="profile-review-input-wrapper">
                            <label>Select Rating Score</label>
                            <select name="rating_value" required>
                                <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent Service</option>
                                <option value="4">⭐⭐⭐⭐ 4 - Good Quality</option>
                                <option value="3">⭐⭐⭐ 3 - Average Work</option>
                                <option value="2">⭐⭐ 2 - Disappointing</option>
                                <option value="1">⭐ 1 - Very Unsatisfactory</option>
                            </select>
                        </div>
                        <div class="profile-review-textarea-wrapper">
                            <label>Write Your Review Comment</label>
                            <textarea name="review_comment" required placeholder="Describe the quality of service, arrival timing, and communication..."></textarea>
                        </div>
                        <button type="submit" class="profile-review-btn-submit">Submit Review Feedback</button>
                    </form>
                </div>
            <?php endif; ?>

            <section class="profile-public-reviews-card">
                <h3>Customer Reviews</h3>
                <?php if (!empty($public_reviews)): ?>
                    <?php foreach ($public_reviews as $r_row): ?>
                        <div class="profile-feed-row">
                            <div class="profile-feed-row-top">
                                <h5><?php echo htmlspecialchars($r_row['customer_name']); ?></h5>
                                <span><?php echo $r_row['formatted_date']; ?></span>
                            </div>
                            <div class="profile-feed-stars"><?php echo $r_row['star_icons']; ?></div>
                            <p class="profile-feed-comment">"<?php echo htmlspecialchars($r_row['comment']); ?>"</p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="profile-feed-empty-log">No client review logs left for this provider yet.</p>
                <?php endif; ?>
            </section>
        </div>

        <aside class="profile-sidebar-card">
            <h3><i class="fas fa-calendar-check" style="color: #28a745;"></i> Book Appointment</h3>
            
            <form action="profile.php?id=<?php echo $provider_id; ?>" method="POST">
                <input type="hidden" name="submit_booking" value="1">
                
                <div class="profile-sidebar-group">
                    <label>Target Appointment Date</label>
                    <input type="date" name="booking_date" required class="filter-input">
                </div>

                <div class="profile-sidebar-group-last">
                    <label>Preferred Arrival Time</label>
                    <input type="time" name="booking_time" required class="filter-input">
                </div>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'customer'): ?>
                    <button type="submit" class="profile-sidebar-btn-book">Send Request</button>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'provider'): ?>
                    <div class="profile-sidebar-fallback-badge">
                        ⚠️ Provider accounts cannot request appointments.
                    </div>
                <?php else: ?>
                    <a href="login.html" class="profile-sidebar-btn-guest-login">Login to Request Service</a>
                <?php endif; ?>
            </form>
        </aside>

    </div>

    <footer class="search-footer" style="margin-top: 80px;">
        <div class="footer-content">
            <h3 class="logo">LocalService<span>Finder</span></h3>
            <p>&copy; 2026 Local Service Finder | BAUST CSE 3200 Project</p>
        </div>
    </footer>
</body>
</html>