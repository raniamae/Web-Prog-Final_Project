
<header class="">

    <div class="navbar">
        <button class="navbar-menu-btn" fdprocessedid="5jbzbb">
              <span class="one"></span>
              <span class="two"></span>
              <span class="three"></span>
              <span class="visually-hidden">Toggle navigation menu</span>
		</button>


        <a href="index.php" class="navbar-logo">
            <img src="img/logo.png" alt="An image of the website's logo">
        </a>

        <nav class="">
            <ul class="navbar-nav">
                <li><a href="index.php" class="navbar-link">Home</a></li>
                
                <li class="navbar-item dropdown">
                    <a href="javascript:void(0)" class="navbar-link dropdown-btn">
                        Category <i class="ri-arrow-down-s-fill"></i>
                    </a>
                    
                    <div class="dropdown-content">
                        <a href="category.php?id=all">All Movies</a>
                        
                        <?php
                        if (isset($conn)) {
                            $cat_sql = "SELECT * FROM categories ORDER BY name ASC";
                            $cat_result = mysqli_query($conn, $cat_sql);

                            if ($cat_result && mysqli_num_rows($cat_result) > 0) {
                                while ($cat_row = mysqli_fetch_assoc($cat_result)) {
                                    
                                    echo '<a href="category.php?id=' . $cat_row['category_id'] . '">' . $cat_row['name'] . '</a>';
                                }
                            }
                        }
                        ?>
                    </div>
                </li>
                <li><a href="collections.php" class="navbar-link">Collections</a></li>
				<li><a href="my_reviews.php" class="navbar-link">My Reviews</a></li>
            </ul>
        </nav>

        <div class="navbar-actions">
			<form action="search_bar.php" method="GET" class="navbar-form">
                <label for="search" class="visually-hidden">Search movies</label>
   				 <input id="search" type="text" class="navbar-form-search" name="q" placeholder="Search movies..." required>
    				<button type="submit" class="navbar-form-btn">
                      <i class="ri-search-2-line"></i>
                      <span class="visually-hidden">Search</span>
					</button>
    				<button type="button" class="navbar-form-close">
                      <i class="ri-close-line"></i>
                      <span class="visually-hidden">Close search</span>
                    </button>
			</form>

            <button class="navbar-search-btn">
              <i class="ri-search-2-line"></i>
              <span class="visually-hidden">Open search</span>
            </button>

           <button class="navbar-user-btn">
              <i class="ri-user-fill"></i>
              <span class="visually-hidden">User menu</span>
            </button>


            <div class="user-box">
                <?php 
                    $avatar_img = "img/default_avatar.png"; 
                    if (isset($_SESSION["avatar_url"]) && !empty($_SESSION["avatar_url"])) {
                        if (file_exists($_SESSION["avatar_url"])) {
                            $avatar_img = $_SESSION["avatar_url"];
                        }
                    }
                ?>

                <img src="<?php echo $avatar_img; ?>" alt="The user's profile picture" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin: 0 auto 15px; display: block; border: 3px solid var(--light-azure, #448ef6); box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                
                <p>Username: <span><?php echo isset($_SESSION["user_name"]) ? $_SESSION["user_name"] : 'Guest'; ?></span></p>
                <p>Email: <span><?php echo isset($_SESSION["user_email"]) ? $_SESSION["user_email"] : ''; ?></span></p>

                <?php if (!isset($_SESSION["user_name"])): ?>
                    <a href="login.php" class="login-btn">Login</a>
                    <a href="register.php" class="register-btn">Register</a>
                <?php else: ?>
                    <a href="profile_update.php" class="profile_update_btn">Update Profile</a>
                    <form method="post" action="logout.php">
                        <button type="submit" name="logout" class="logout-btn">Logout</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<script src="final.js"></script>