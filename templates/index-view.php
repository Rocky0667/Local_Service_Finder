<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Service Finder | Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/index-style.css">
</head>
<body>

    <header>
        <nav class="navbar" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="index.php" class="logo">LocalService<span>Finder</span></a>
            <ul class="nav-links" style="display: flex; align-items: center; gap: 25px; list-style: none;">
                <li><a href="search.php">Find Services</a></li>
                <li><a href="#about-project">About</a></li>
                
                <?php if ($is_logged_in): ?>
                    <li style="display: flex; align-items: center; gap: 15px; margin: 0; padding: 0;">
                        <a href="<?php echo $target_dashboard; ?>" style="display: block; width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 2px solid #1a2a6c; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
                            <img src="<?php echo $nav_avatar_path; ?>?v=<?php echo time(); ?>" alt="Dashboard Portal Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        </a>
                        <a href="logout.php" style="color: #dc3545; font-weight: 600; text-decoration: none; font-size: 14px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                <?php else: ?>
                    <li><a href="register.html">Become a Provider</a></li>
                    <li><a href="login.html" class="login-btn">Login</a></li>
                    <li><a href="register.html" class="register-btn">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content" style="width: 100%;">
            <h1>Find expert help for your home and office</h1>
            
            <form action="search.php" method="GET" class="search-bar-wrapper" style="width: 100%; max-width: 950px; margin: 0 auto; display: flex; align-items: center; margin-top: 30px;">
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" name="service" placeholder="What service do you need?" id="service-search">
                </div>
                <div class="divider"></div>
                <div class="search-input-group">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="location" placeholder="Enter your division" id="location-search">
                </div>
                <button type="submit" class="hero-search-button" style="white-space: nowrap;">Search Now</button>
            </form>
        </div>
    </section>

    <div class="slide-container slide-in-left">
        <section class="categories">
            <h2>Popular Categories</h2>
            <div class="category-grid scrollable-wrapper">
                <a href="search.php?service=Plumbing" class="category-link-card">
                    <div class="category-card">
                        <i class="fas fa-tools"></i>
                        <p>Plumbing</p>
                    </div>
                </a>
                <a href="search.php?service=Electrical" class="category-link-card">
                    <div class="category-card">
                        <i class="fas fa-bolt"></i>
                        <p>Electrical</p>
                    </div>
                </a>
                <a href="search.php?service=Tutoring" class="category-link-card">
                    <div class="category-card">
                        <i class="fas fa-book"></i>
                        <p>Tutoring</p>
                    </div>
                </a>
                <a href="search.php?service=Mechanic" class="category-link-card">
                    <div class="category-card">
                        <i class="fas fa-car-side"></i>
                        <p>Mechanic</p>
                    </div>
                </a>
                <a href="search.php?service=Painting" class="category-link-card">
                    <div class="category-card">
                        <i class="fas fa-paint-roller"></i>
                        <p>Painting</p>
                    </div>
                </a>
            </div>
        </section>
    </div>

    <div class="slide-container slide-in-left" style="animation-delay: 0.2s;">
        <section class="providers">
            <h2>Top Verified Professionals</h2>
            <div class="provider-grid scrollable-wrapper">
                <?php if (!empty($top_providers)): ?>
                    <?php foreach ($top_providers as $p_row): ?>
                        <div class="provider-card" style="text-align: center;">
                            <div class="provider-img-fixed">
                                <img src="<?php echo $p_row['avatar_render_path']; ?>?v=<?php echo time(); ?>" alt="Provider Image">
                            </div>
                            <div class="provider-info">
                                <h3><?php echo htmlspecialchars($p_row['name']); ?></h3>
                                <p class="specialty"><?php echo htmlspecialchars($p_row['specialty']); ?></p>
                                <div class="rating" style="justify-content: center; margin-bottom: 15px;">
                                    <i class="fas fa-star" style="color: #fdbb2d;"></i> <?php echo number_format($p_row['rating'], 1); ?> <span>(Verified Pro)</span>
                                </div>
                                <a href="profile.php?id=<?php echo $p_row['id']; ?>"><button class="view-btn">View Profile</button></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#777; padding: 20px 0; text-align: center; width: 100%;">No service providers have registered profiles yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <footer id="about-project">
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