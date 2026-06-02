<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Services | Local Service Finder</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/search-style.css">
</head>
<body>

    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">LocalService<span>Finder</span></a>
            <ul class="nav-links">
                <li><a href="search.php" class="active-nav">Find Services</a></li>
                <li><a href="index.php#about-project">About</a></li>
                <?php
                if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'customer') {
                    echo '<li><a href="user-dashboard.php" class="active-nav"><i class="fas fa-user-circle"></i> Dashboard</a></li>';
                    echo '<li><a href="logout.php" style="color: #dc3545;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>';
                } else if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'provider') {
                    echo '<li><a href="provider-dashboard.php" class="active-nav"><i class="fas fa-store"></i> Provider Portal</a></li>';
                    echo '<li><a href="logout.php" style="color: #dc3545;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>';
                } else {
                    echo '<li><a href="register.html">Become a Provider</a></li>';
                    echo '<li><a href="login.html" class="login-btn">Login</a></li>';
                    echo '<li><a href="register.html" class="register-btn">Register</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>

    <section class="search-banner">
        <h1>Search Results</h1>
        <p>Showing the best verified professionals in your area</p>
    </section>

    <div class="search-container slide-in-left">
        
        <aside class="filters-sidebar">
            <h3>Filters</h3>
            <form action="search.php" method="GET">
                <div class="filter-group">
                    <label>Category / Service</label>
                    <input type="text" name="service" class="filter-input" placeholder="e.g. Electrician, Plumber" value="<?php echo htmlspecialchars($search_service); ?>">
                </div>

                <div class="filter-group">
                    <label>Location (Division)</label>
                    <select name="location" class="filter-input">
                        <option value="">All Bangladesh</option>
                        <option value="Dhaka" <?php if($search_location == 'Dhaka') echo 'selected'; ?>>Dhaka</option>
                        <option value="Chittagong" <?php if($search_location == 'Chittagong') echo 'selected'; ?>>Chittagong</option>
                        <option value="Rajshahi" <?php if($search_location == 'Rajshahi') echo 'selected'; ?>>Rajshahi</option>
                        <option value="Khulna" <?php if($search_location == 'Khulna') echo 'selected'; ?>>Khulna</option>
                        <option value="Barisal" <?php if($search_location == 'Barisal') echo 'selected'; ?>>Barisal</option>
                        <option value="Sylhet" <?php if($search_location == 'Sylhet') echo 'selected'; ?>>Sylhet</option>
                        <option value="Rangpur" <?php if($search_location == 'Rangpur') echo 'selected'; ?>>Rangpur</option>
                        <option value="Mymensingh" <?php if($search_location == 'Mymensingh') echo 'selected'; ?>>Mymensingh</option>
                    </select>
                </div>

                <button type="submit" class="auth-btn apply-filters-btn">Apply Filters</button>
            </form>
        </aside>

        <main class="results-area">
            <?php if (!empty($search_records)): ?>
                <?php foreach ($search_records as $row): ?>
                    <div class="search-result-card">
                        <div class="result-img-box">
                            <img src="<?php echo $row['avatar_render_path']; ?>?v=<?php echo time(); ?>" alt="Provider Profile Image">
                        </div>
                        <div class="result-info">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="result-specialty"><?php echo htmlspecialchars($row['specialty']); ?></p>
                            <p class="result-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['service_area']); ?>, Bangladesh</p>
                            <div class="result-rating">
                                <i class="fas fa-star" style="color: #fdbb2d;"></i> <?php echo number_format($row['rating'], 1); ?> <span>(Verified Professional)</span>
                            </div>
                        </div>
                        <div class="result-action">
                            <a href="profile.php?id=<?php echo $row['id']; ?>">
                                <button class="view-btn">View Profile</button>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="search-empty-state-card">
                    <i class="fas fa-search"></i>
                    <h3>No Professionals Found</h3>
                    <p>We couldn't find any service providers matching your exact criteria in that division yet.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <footer class="search-footer">
        <div class="footer-content">
            <h3 class="logo">LocalService<span>Finder</span></h3>
            <p>Trusted by thousands of users for quality local services.</p>
            <div class="footer-bottom">
                <p>&copy; 2026 Local Service Finder | BAUST CSE 3200 Project</p>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html>