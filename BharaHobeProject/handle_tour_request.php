<?php
session_start();
require_once('dbconnect.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];

//renter username
$sql_user = "SELECT username FROM users WHERE email='$email'";
$res_user = mysqli_query($conn, $sql_user);
if (mysqli_num_rows($res_user) == 0) exit("User not found.");
$renter_username = mysqli_fetch_assoc($res_user)['username'];

//tour_id is provided
if (!isset($_POST['tour_id'])) {
    exit("No tour selected.");
}

$tour_id = (int) $_POST['tour_id'];

// inserting history with function
function add_history($conn, $username, $event) {
    $username = mysqli_real_escape_string($conn, $username);
    $event = mysqli_real_escape_string($conn, $event);
    mysqli_query($conn, "INSERT INTO history (username, event, date_time) VALUES ('$username', '$event', NOW())");
}

//advert_id and tenant for this tour
$res_tour = mysqli_query($conn, "
    SELECT st.advert_id, st.tenant_username 
    FROM scheduled_tour st 
    WHERE st.tour_id = $tour_id
");
if (mysqli_num_rows($res_tour) == 0) exit("Tour not found.");
$tour_info = mysqli_fetch_assoc($res_tour);
$advert_id = $tour_info['advert_id'];
$tenant_username = $tour_info['tenant_username'];

// accept
if (isset($_POST['accept'])) {
    $sql = "UPDATE tour SET renter_approved='Accepted' WHERE tour_id=$tour_id";
    if (mysqli_query($conn, $sql)) {
        add_history($conn, $renter_username, "Accepted Tour Request for Apartment #$advert_id");
        header("Location: profile.php");
        exit;
    } else {
        exit("Error updating tour: " . mysqli_error($conn));
    }
}

//reject
if (isset($_POST['reject'])) {
    // delete from scheduled_tour
    if (!mysqli_query($conn, "DELETE FROM scheduled_tour WHERE tour_id=$tour_id")) {
        exit("Error deleting from scheduled_tour: " . mysqli_error($conn));
    }

    // delete from tour table
    if (!mysqli_query($conn, "DELETE FROM tour WHERE tour_id=$tour_id")) {
        exit("Error deleting from tour: " . mysqli_error($conn));
    }

    add_history($conn, $renter_username, "Rejected Tour Request for Apartment #$advert_id");
    add_history($conn, $tenant_username, "Your Tour Request for Apartment #$advert_id was Rejected");

    header("Location: profile.php");
    exit;
}
?>
