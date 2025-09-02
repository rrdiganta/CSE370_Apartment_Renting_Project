<?php
session_start();
require_once('dbconnect.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$logged_in = isset($_SESSION['email']);

if (!isset($_GET['id'])) {
    echo "No apartment selected.";
    exit;
}
$advert_id = $_GET['id'];

if (isset($_POST['tour_date']) && isset($_POST['tour_time'])) {
    $tour_date = $_POST['tour_date'];
    $tour_time = $_POST['tour_time'];

    $insert_tour_sql = "INSERT INTO tour (date, time) VALUES ('$tour_date', '$tour_time')";
    if (mysqli_query($conn, $insert_tour_sql)) {
        $tour_id = mysqli_insert_id($conn);

        // Add tenant_username to satisfy foreign key
        mysqli_query($conn, "INSERT INTO scheduled_tour (tour_id, advert_id, tenant_username) VALUES ('$tour_id', '$advert_id', '$username')");

        $user_event = "Scheduled Tour for Apartment #$advert_id";
        mysqli_query($conn, "INSERT INTO history (username, event) VALUES ('$username', '$user_event')");

        $renter_result = mysqli_query($conn, "SELECT renter_username FROM apartment WHERE advert_id='$advert_id' LIMIT 1");
        if ($renter_row = mysqli_fetch_assoc($renter_result)) {
            $renter_username = $renter_row['renter_username'];
            $renter_event = "$username has requested a Tour for Apartment #$advert_id";
            mysqli_query($conn, "INSERT INTO history (username, event) VALUES ('$renter_username', '$renter_event')");
        }

        $success = "Tour scheduled successfully!";
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
<title>Schedule Tour - BharaHobe</title>
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
			  <li><a href="apartments.php">Apartments</a></li>
			  <li><a href="publishad.php">Publish Ad</a></li>
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
		<h2>Schedule a Tour for Apartment #<?= htmlspecialchars($advert_id) ?></h2>

		<?php if(!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
		<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

		<form action="" method="post">
			<div class="row">
				<div class="column">
					<span>Tour Date</span>
					<input type="date" name="tour_date" required>
				</div>
				<div class="column">
					<span>Tour Time</span>
					<input type="time" name="tour_time" required>
				</div>
			</div>
			<input type="submit" value="Schedule Tour">
		</form>
	</div>
</body>
</html>
