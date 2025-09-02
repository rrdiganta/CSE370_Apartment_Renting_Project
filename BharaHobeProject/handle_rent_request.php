<?php
session_start();
require_once('dbconnect.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$renter_username = $_SESSION['username'];

if (!isset($_POST['advert_id'], $_POST['tenant_username'])) {
    echo "Invalid request.";
    exit;
}

$advert_id = $_POST['advert_id'];
$tenant_username = $_POST['tenant_username'];

$status = '';
if (isset($_POST['accept'])) {
    $status = 'Accepted';
} elseif (isset($_POST['reject'])) {
    $status = 'Rejected';
} else {
    echo "No action specified.";
    exit;
}

mysqli_query($conn, "
    UPDATE request_apply
    SET status='$status'
    WHERE advert_id='$advert_id' AND tenant_username='$tenant_username'
");

$history_renter = "$status rental request for Apartment #$advert_id from $tenant_username";
mysqli_query($conn, "
    INSERT INTO history (username, event) VALUES ('$renter_username', '$history_renter')
");

$history_tenant = "Your rental request for Apartment #$advert_id was $status";
mysqli_query($conn, "
    INSERT INTO history (username, event) VALUES ('$tenant_username', '$history_tenant')
");

header("Location: profile.php");
exit;
?>
