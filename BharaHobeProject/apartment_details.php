<?php
session_start();
require_once('dbconnect.php');

$logged_in = false;
$username = null;
if (isset($_SESSION['email'])) {
    $logged_in = true;
    $username = $_SESSION['username'];
}

if (!isset($_GET['id'])) {
    echo "No apartment selected.";
    exit;
}

$advert_id = $_GET['id'];

$sql = "SELECT * FROM apartment WHERE advert_id = '$advert_id'";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Apartment not found.";
    exit;
}

$apartment = mysqli_fetch_assoc($result);

$img_sql = "SELECT image FROM images WHERE advert_id = '$advert_id' ORDER BY img_id ASC LIMIT 1";
$img_result = mysqli_query($conn, $img_sql);

if ($img_result && mysqli_num_rows($img_result) > 0) {
    $img_row = mysqli_fetch_assoc($img_result);
    $img_data = 'data:image/jpeg;base64,' . base64_encode($img_row['image']);
} else {
    $img_data = 'p1.jpg'; 
}

$reviews_result = mysqli_query($conn, "
    SELECT r.review, r.rating, u.username
    FROM review r
    JOIN scheduled_tour st ON r.tour_id = st.tour_id
    JOIN users u ON st.tenant_username = u.username
    WHERE st.advert_id='$advert_id'
    ORDER BY r.tour_id DESC
");

// check if logged-in user can request rent
$can_request_rent = false;
if ($logged_in) {
    $tour_check = mysqli_query($conn, "
        SELECT t.tour_id
        FROM scheduled_tour st
        JOIN tour t ON st.tour_id = t.tour_id
        WHERE st.advert_id='$advert_id' 
          AND st.tenant_username='$username' 
          AND t.status='Completed'
        LIMIT 1
    ");
    if (mysqli_num_rows($tour_check) > 0) {
        $can_request_rent = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0 ">
<title>Apartment Details - BharaHobe</title>
<link rel="stylesheet" href="css/style.css?v=1.5">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
</head>
<body class="profile-page">

<header>
<div class="nav container">
    <a href="index.php" class="logo"><i class='bx bxs-building-house'></i>BharaHobe</a>
    <input type="checkbox" id="menu">
    <label for="menu"><i class='bx bxs-menu' id="menu-icon"></i></label>
    <ul class="navbar">
        <li><a href="index.php">Home</a></li>
        <li><a href="apartments.php">Apartments</a></li>
        <li><a href="publishad.php">Publish Ad</a></li>
    </ul>
    <?php if ($logged_in): ?>
        <span class="welcome">Welcome, <?= htmlspecialchars($username); ?>!</span>
        <a href="profile.php" class="btn">Profile</a>
        <a href="logout.php" class="btn">Logout</a>
    <?php else: ?>
        <a href="login.php" class="btn">Log In</a>
        <a href="signup.php" class="btn">Sign Up</a>
    <?php endif; ?>
</div>
</header>

<main class="profile-container">

<!-- left -->
<div class="profile-info">
    <h2>Apartment Info</h2>
    <p><strong>Advert ID:</strong> <?= htmlspecialchars($apartment['advert_id']); ?></p>
    <p><strong>Renter Username:</strong> <?= htmlspecialchars($apartment['renter_username']); ?></p>
    <p><strong>Bedrooms:</strong> <?= htmlspecialchars($apartment['no_of_bed']); ?></p>
    <p><strong>Bathrooms:</strong> <?= htmlspecialchars($apartment['no_of_wash']); ?></p>
    <p><strong>Available From:</strong> <?= htmlspecialchars($apartment['available_from']); ?></p>
    <p><strong>District:</strong> <?= htmlspecialchars($apartment['PO_District']); ?></p>
    <p><strong>Road:</strong> <?= htmlspecialchars($apartment['Road']); ?></p>
    <p><strong>House No:</strong> <?= htmlspecialchars($apartment['House_no']); ?></p>
    <p><strong>Rent:</strong> <?= htmlspecialchars($apartment['rent']); ?> BDT</p>
    <p><strong>Area:</strong> <?= htmlspecialchars($apartment['area']); ?></p>
    <p><strong>Type:</strong> <?= htmlspecialchars($apartment['apt_type']); ?></p>
</div>

<!-- middle -->
<div class="profile-history">
    <h2>Apartment Details</h2>
    <img src="<?= $img_data; ?>" alt="Apartment Image" style="border-radius:12px; margin-bottom:15px;">
    <p><?= nl2br(htmlspecialchars($apartment['description'])); ?></p>

    <div style="margin-top:30px;">
        <h3>Reviews</h3>
        <?php if(mysqli_num_rows($reviews_result) > 0): ?>
            <?php while($review = mysqli_fetch_assoc($reviews_result)): ?>
                <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px; border-radius:8px;">
                    <p><strong><?= htmlspecialchars($review['username']); ?></strong> rated <strong><?= htmlspecialchars($review['rating']); ?>/5</strong></p>
                    <p><?= nl2br(htmlspecialchars($review['review'])); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>
    </div>
</div>

<!-- right -->
<div class="profile-empty" style="display:flex; align-items:center; justify-content:center;">
    <div style="display:flex; flex-direction: column; gap: 15px; width: 100%; max-width: 200px; align-items: center;">
        <?php if ($logged_in): ?>
            <a href="schedule_tour.php?id=<?= $apartment['advert_id']; ?>" class="btn" style="width:100%; text-decoration:none;">
                Schedule a Tour
            </a>

            <a href="renter_profile.php?username=<?= urlencode($apartment['renter_username']); ?>" class="btn" style="width:100%; text-decoration:none;">
                View Renter's Profile
            </a>

            <?php if($can_request_rent): ?>
                <form action="request_rent.php" method="POST" style="width:100%; margin-top:0;">
                    <input type="hidden" name="advert_id" value="<?= $apartment['advert_id']; ?>">
                    <input type="hidden" name="renter_username" value="<?= htmlspecialchars($apartment['renter_username']); ?>">
                    <button type="submit" class="btn" style="width:100%; border:none; outline:none;"> Request Rental </button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <p style="color:red; text-align:center;">Please log in to schedule a tour or view renter profile.</p>
        <?php endif; ?>
    </div>
</div>
</main>
</body>
</html>
