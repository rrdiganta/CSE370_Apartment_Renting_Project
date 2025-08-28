<?php
session_start();
require_once('dbconnect.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$logged_in = isset($_SESSION['email']);

if (isset($_POST['rent']) && isset($_POST['Road'])) {

    $no_of_bed      = $_POST['no_of_bed'];
    $no_of_wash     = $_POST['no_of_wash'];
    $available_from = $_POST['available_from'];
    $PO_District    = $_POST['PO_District'];
    $Road           = $_POST['Road'];
    $House_no       = $_POST['House_no'];
    $rent           = $_POST['rent'];
    $area           = $_POST['area'];
    $apt_type       = $_POST['apt_type'];
    $description    = $_POST['description'];

    $insert_sql = "INSERT INTO apartment 
        (renter_username, no_of_bed, no_of_wash, available_from, PO_District, Road, House_no, rent, area, apt_type, description)
        VALUES ('$username', '$no_of_bed', '$no_of_wash', '$available_from', '$PO_District', '$Road', '$House_no', '$rent', '$area', '$apt_type', '$description');";

    if (mysqli_query($conn, $insert_sql)) {
        $advert_id = mysqli_insert_id($conn);
        mysqli_query($conn, "UPDATE users SET ren_flag = 1 WHERE username = '$username';");
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $tmp_name) {
                mysqli_query($conn, "INSERT INTO images (advert_id, image) VALUES ('$advert_id', '".addslashes(file_get_contents($tmp_name))."')");
            }
        }

        $event_msg = "Listed Apartment #$advert_id for rental";
        mysqli_query($conn, "INSERT INTO history (username, event) VALUES ('$username', '$event_msg')");

        $success = "Advertisement published successfully!";
    } 
	else {
        $error = "Something went wrong. Please try again.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Publish Ad - BharaHobe</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
</head>
<body class="publish-page">
	<header>
        <div class="nav container">
            <a href="index.php" class="logo"><i class='bx  bxs-building-house'></i>BharaHobe</a>
            <input type="checkbox" name="" id="menu">
            <label for="menu"><i class='bx  bxs-menu' id="menu-icon"></i></label>
            <ul class="navbar">
              <li><a href="index.php">Home</a></li>                            
              <li><a href="#Services">Services</a></li>
			  <li><a href="#Apartments">Apartments</a></li>
			  <li><a href="publishad.php">Publish Ad</a></li>
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

	<div class="publish-container">
		<h2>Publish Apartment Advertisement</h2>

		<?php if(!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
		<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

		<form action="" method="post" enctype="multipart/form-data">
			<div class="row">
				<div class="column">
					<span>No. of Bedrooms</span>
					<input type="number" name="no_of_bed" required>
				</div>
				<div class="column">
					<span>No. of Washrooms</span>
					<input type="number" name="no_of_wash" required>
				</div>
			</div>

			<div class="row">
				<div class="column">
					<span>Available From</span>
					<input type="date" name="available_from" required>
				</div>
				<div class="column">
					<span>Post Office and District</span>
					<input type="text" name="PO_District" required>
				</div>
			</div>

			<div class="row">
				<div class="column">
					<span>Road</span>
					<input type="text" name="Road" required>
				</div>
				<div class="column">
					<span>House No.</span>
					<input type="text" name="House_no" required>
				</div>
			</div>

			<div class="row">
				<div class="column">
					<span>Rent (BDT)</span>
					<input type="number" name="rent" required>
				</div>
				<div class="column">
					<span>Area (sqft)</span>
					<input type="number" name="area" required>
				</div>
			</div>

			<div class="row">
				<div class="column">
					<span>Apartment Type</span>
					<select name="apt_type" required>
						<option value="Sublet">Sublet</option>
						<option value="Full Unit">Full Unit</option>
					</select>
				</div>
			</div>

			<div class="row">
				<div class="column">
					<span>Description</span>
					<textarea name="description" rows="5" required></textarea>
				</div>
			</div>

			<div class="row">
				<div class="column">
					<span>Upload Images</span>
					<input type="file" name="images[]" multiple>
				</div>
			</div>

			<input type="submit" value="Publish Advertisement">
		</form>
	</div>
</body>
</html>
