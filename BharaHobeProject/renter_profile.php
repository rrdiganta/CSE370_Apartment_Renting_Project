<?php
session_start();
require_once('dbconnect.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$logged_in_email = $_SESSION['email'];
$logged_in_username = $_SESSION['username'];

if (!isset($_GET['username'])) {
    echo "No user selected.";
    exit;
}

$profile_username = $_GET['username'];

// renter info
$sql = "SELECT username, email, date_of_birth, Phone_no, nid, ten_type, ren_flag FROM users WHERE username='$profile_username'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "User not found.";
    exit;
}

$user = mysqli_fetch_assoc($result);

// history of user in session
$history_sql = "SELECT * FROM history WHERE username='$profile_username' ORDER BY date_time DESC";
$history_result = mysqli_query($conn, $history_sql);

// renter's ads
$apartments_result = mysqli_query($conn, "
    SELECT advert_id, apt_type, rent, no_of_bed, no_of_wash, area, available_from
    FROM apartment
    WHERE renter_username='$profile_username'
    ORDER BY advert_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0 ">
<title>Profile - BharaHobe</title>
<link rel="stylesheet" href="css/style.css">
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
            <li><a href="#Services">Services</a></li>
            <li><a href="apartments.php">Apartments</a></li>
            <li><a href="publishad.php">Publish Ad</a></li>
        </ul>
        <span class="welcome">Welcome, <?= htmlspecialchars($logged_in_username); ?>!</span>
        <a href="profile.php" class="btn">Profile</a>
        <a href="logout.php" class="btn">Logout</a>
    </div>
</header>

<main class="profile-container">

<!-- profile -->
<div class="profile-info">
    <h2>Profile Information</h2>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($user['date_of_birth']); ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($user['Phone_no']); ?></p>
    <p><strong>NID:</strong> <?= htmlspecialchars($user['nid']); ?></p>
    <p><strong>Tenant Type:</strong></p>
    <?php if ($logged_in_username === $user['username']): ?>
        <form method="POST" action="" class="tenant-form">
            <select name="ten_type" class="tenant-type">
                <option value="" disabled <?php if (empty($user['ten_type'])) echo 'selected'; ?>>Select Tenant Type</option>
                <option value="Family" <?php if ($user['ten_type'] == 'Family') echo 'selected'; ?>>Family</option>
                <option value="Bachelor" <?php if ($user['ten_type'] == 'Bachelor') echo 'selected'; ?>>Bachelor</option>
            </select>
            <button type="submit" name="update_ten_type" class="tenant-type-button">Update</button>
        </form>
    <?php else: ?>
        <p><?= htmlspecialchars($user['ten_type'] ?? 'Not set'); ?></p>
    <?php endif; ?>
</div>

<!-- history -->
<div class="profile-history">
    <h2>History</h2>
    <?php if (mysqli_num_rows($history_result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($history_result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['event']); ?></td>
                        <td><?= htmlspecialchars($row['date_time']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No history available.</p>
    <?php endif; ?>
</div>

<!-- renter's ads -->
<div class="profile-empty">
    <h2>Listings by <?= htmlspecialchars($user['username']); ?></h2>
    <?php if (mysqli_num_rows($apartments_result) > 0): ?>
        <?php while ($apt = mysqli_fetch_assoc($apartments_result)): ?>
            <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px; border-radius:8px;">
                <p><strong>Advert ID:</strong> <?= htmlspecialchars($apt['advert_id']); ?></p>
                <p><strong>Type:</strong> <?= htmlspecialchars($apt['apt_type']); ?></p>
                <p><strong>Bedrooms:</strong> <?= htmlspecialchars($apt['no_of_bed']); ?> | 
                   <strong>Bathrooms:</strong> <?= htmlspecialchars($apt['no_of_wash']); ?></p>
                <p><strong>Area:</strong> <?= htmlspecialchars($apt['area']); ?> | 
                   <strong>Rent:</strong> <?= htmlspecialchars($apt['rent']); ?> BDT</p>
                <p><strong>Available From:</strong> <?= htmlspecialchars($apt['available_from']); ?></p>
                <a href="apartment_details.php?id=<?= $apt['advert_id']; ?>" class="btn" style="width:100%; text-align:center;">View Apartment</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No listings available.</p>
    <?php endif; ?>
</div>

</main>
</body>
</html>
