<?php
session_start();

$logged_in = isset($_SESSION['email']);
if ($logged_in) {
    $username = $_SESSION['username'];
} 
else {
    $username = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 ">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <title>BharaHobe</title>
</head>
<body>
    <header>
        <div class="nav container">
            <a href="index.php" class="logo"><i class='bx  bxs-building-house'></i>BharaHobe</a>
            <input type="checkbox" name="" id="menu">
            <label for="menu"><i class='bx  bxs-menu' id="menu-icon"></i></label>
            <ul class="navbar">
				<?php if ($logged_in): ?>
   				  <li><a href="index.php">Home</a></li>                            
				  <li><a href="#Services">Services</a></li>
				  <li><a href="#Apartments">Apartments</a></li>
				  <li><a href="publishad.php">Publish Ad</a></li>
				<?php else: ?>
   				  <li><a href="index.php">Home</a></li>                            
				  <li><a href="login.php">Services</a></li>
				  <li><a href="login.php">Apartments</a></li>
				  <li><a href="login.php">Publish Ad</a></li>
				<?php endif; ?>
			  <!--<li><a href="#About">About Us</a></li> -->
            </ul>
            <?php if ($logged_in): ?>
			  <span class="welcome">Welcome, <?= htmlspecialchars($username) ?>!</span>
			  <a href="profile.php" class="btn">Profile</a>
			  <a href="logout.php" class="btn">Logout</a>
			<?php else: ?>
			  <a href="login.php" class="btn">Log In</a>
			  <a href="signup.php" class="btn">Sign Up</a>
			<?php endif; ?>
		</div>
    </header>
    <section class="home container" id="home">
        <div class="home-text">
            <h1>Your Dream<br>House Is HERE!</h1>
            <form>
                <input type="text" placeholder="Search your home here">
                <button type="submit"><i class="bx  bxs-search"></i></button>
            </form>
        </div>
    </section>
    <section class="about container" id="about">
        <div class="about-img">
            <img src="img/about.jpg" alt="">
        </div>
        <div class="about-text">
            <span>About Us</span>
            <h2>We Provide The Best<br>Apartment For You!</h2>
            <p>Welcome to BharaHobe, your trusted partner in finding the perfect place to call home.</p>
            <p>We understand that searching for the right apartment isn’t just about four walls—it’s about comfort, convenience and a lifestyle that suits you. That’s why we’ve created a platform that makes renting simple, transparent, and stress-free.</p>
            <p>Our mission is simple: To connect people with homes that match their needs and make the renting journey as smooth as possible.</p>
            <p>We also offer tours of these apartments before you can finalize.</p>
            <a href="#" class="btn">Learn More</a>
        </div>
    </section>
        
</body>
</html>