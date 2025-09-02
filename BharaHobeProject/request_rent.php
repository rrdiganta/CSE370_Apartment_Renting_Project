<?php
session_start();
require_once('dbconnect.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

if (!isset($_POST['advert_id']) || !isset($_POST['renter_username'])) {
    echo "Invalid request.";
    exit;
}

$advert_id = $_POST['advert_id'];
$renter_username = $_POST['renter_username'];

// Check if the user already made a request
$check_sql = "SELECT * FROM request_apply 
              WHERE advert_id='$advert_id' AND tenant_username='$username' LIMIT 1";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    echo "You have already requested this apartment.";
    exit;
}

// Insert rental request
$insert_sql = "INSERT INTO request_apply (advert_id, renter_username, tenant_username) 
               VALUES ('$advert_id', '$renter_username', '$username')";

if (mysqli_query($conn, $insert_sql)) {
    echo "Rental request submitted successfully!";
    // Optionally redirect back to apartment page
    header("Location: apartment_details.php?id=$advert_id");
    exit;
} else {
    echo "Something went wrong. Please try again.";
    exit;
}
?>
