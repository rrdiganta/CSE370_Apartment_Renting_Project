<?php
session_start();
require_once('dbconnect.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['tour_id'])) {
    echo "No tour selected.";
    exit;
}

$tour_id = $_POST['tour_id'];

// retrieve advert_id to pass data to review page
$result = mysqli_query($conn, "SELECT advert_id FROM scheduled_tour WHERE tour_id='$tour_id' LIMIT 1");
if ($row = mysqli_fetch_assoc($result)) {
    $advert_id = $row['advert_id'];
    // going to review page
    header("Location: review.php?tour_id=$tour_id&advert_id=$advert_id");
    exit;
} else {
    echo "Tour not found.";
    exit;
}
?>

