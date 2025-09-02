<?php
session_start();
require_once('dbconnect.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

if (!isset($_GET['tour_id'], $_GET['advert_id'])) {
    echo "Invalid request.";
    exit;
}

$tour_id = $_GET['tour_id'];
$advert_id = $_GET['advert_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    // Insert review
    $sql = "INSERT INTO review (tour_id, review, rating) VALUES ('$tour_id', '$comment', '$rating')";
    mysqli_query($conn, $sql);

    // Mark tour completed
    mysqli_query($conn, "UPDATE tour SET status='Completed' WHERE tour_id='$tour_id'");

    // Add history entries
    $tenant_event = "Completed tour and submitted review for Apartment #$advert_id";
    mysqli_query($conn, "INSERT INTO history (username, event) VALUES ('$username', '$tenant_event')");

    $renter_result = mysqli_query($conn, "SELECT renter_username FROM apartment WHERE advert_id='$advert_id' LIMIT 1");
    if ($row = mysqli_fetch_assoc($renter_result)) {
        $renter_event = "$username completed the tour and submitted a review for Apartment #$advert_id";
        mysqli_query($conn, "INSERT INTO history (username, event) VALUES ('{$row['renter_username']}', '$renter_event')");
    }

    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submit Review - BharaHobe</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
</head>
<body class="publish-page">
    <header>
        <div class="nav container">
            <a href="index.php" class="logo"><i class='bx  bxs-building-house'></i>BharaHobe</a>
            <input type="checkbox" id="menu">
            <label for="menu"><i class='bx  bxs-menu' id="menu-icon"></i></label>
            <ul class="navbar">
                <li><a href="index.php">Home</a></li>
                <li><a href="apartments.php">Apartments</a></li>
                <li><a href="publishad.php">Publish Ad</a></li>
            </ul>
            <span class="welcome">Welcome, <?= htmlspecialchars($username) ?>!</span>
            <a href="profile.php" class="btn">Profile</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </header>

    <div class="publish-container">
        <h2>Submit Review for Apartment #<?= htmlspecialchars($advert_id) ?></h2>

        <form action="" method="post">
            <div class="row">
                <div class="column">
                    <span>Rating (1-5)</span>
                    <input type="number" name="rating" min="1" max="5" required>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <span>Review</span>
                    <textarea name="comment" rows="4" required></textarea>
                </div>
            </div>
            <input type="submit" value="Submit Review">
        </form>
    </div>
</body>
</html>
