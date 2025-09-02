<?php
session_start(); 
require_once('dbconnect.php'); 

$logged_in = false;
$username = null;

if (isset($_SESSION['email'])) {
    $logged_in = true;
    $username = $_SESSION['username'];

    if ($username === '' || strtolower($username) === 'root') {
        $username = 'Guest';
    }
}

$search = '';
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search = mysqli_real_escape_string($conn, $_GET['q']);
    $sql = "SELECT advert_id, no_of_bed, no_of_wash, available_from, rent, Road 
            FROM apartment 
            WHERE Road LIKE '%$search%' OR advert_id LIKE '%$search%' 
            ORDER BY advert_id DESC";
} else {
    $sql = "SELECT advert_id, no_of_bed, no_of_wash, available_from, rent, Road 
            FROM apartment 
            ORDER BY advert_id DESC";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0 ">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="stylesheet" href="css/style.css?v=1.5"> 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
<title>BharaHobe</title>
</head>
<body>

<header>
<div class="nav container">
    <a href="index.php" class="logo"><i class='bx  bxs-building-house'></i>BharaHobe</a>
    <input type="checkbox" id="menu">
    <label for="menu"><i class='bx  bxs-menu' id="menu-icon"></i></label>
    <ul class="navbar">
        <?php if ($logged_in): ?>
            <li><a href="index.php">Home</a></li>
            <li><a href="#Services">Services</a></li>
            <li><a href="apartments.php">Apartments</a></li>
            <li><a href="publishad.php">Publish Ad</a></li>
        <?php else: ?>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Services</a></li>
            <li><a href="apartments.php">Apartments</a></li>
            <li><a href="login.php">Publish Ad</a></li>
        <?php endif; ?>
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


<section class="search container">
    <form action="apartments.php" method="get" class="search-box">
        <input type="text" name="q" placeholder="Search apartments..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit"><i class='bx bxs-search'></i></button>
    </form>
</section>

<section class="properties container" id="properties">
    <div class="heading">
        <h2>Featured Properties</h2>
    </div>
	<div class="properties-grid">
		<?php
		if($result && mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_assoc($result)){
				$img_sql = "SELECT image FROM images WHERE advert_id = ".$row['advert_id']." ORDER BY img_id ASC LIMIT 1";
				$img_result = mysqli_query($conn, $img_sql);

				if($img_result && mysqli_num_rows($img_result) > 0){
					$img_row = mysqli_fetch_assoc($img_result);
					$img_data = 'data:image/jpeg;base64,'.base64_encode($img_row['image']);
				} 
				else {
					$img_data = 'p1.jpg';
				}

				echo '<div class="property-box">';

				if ($logged_in) {
					echo '<a href="apartment_details.php?id='.$row['advert_id'].'">';
				}

				echo '<img src="'.$img_data.'" alt="Apartment">';
				echo '<h3>Apartment #'.$row['advert_id'].'</h3>';
				echo '<p><strong>Bedrooms:</strong> '.$row['no_of_bed'].'</p>';
				echo '<p><strong>Bathrooms:</strong> '.$row['no_of_wash'].'</p>';
				echo '<p><strong>Available:</strong> '.$row['available_from'].'</p>';
				echo '<p><strong>Rent:</strong> '.$row['rent'].' BDT</p>';
				echo '<p><strong>Road:</strong> '.$row['Road'].'</p>';

				if ($logged_in) {
					echo '</a>';
				}

				echo '</div>';
			}
		} 
		else {
			echo '<p>No apartments found.</p>';
		}
		?>
	</div>
</section>

</body>
</html>
