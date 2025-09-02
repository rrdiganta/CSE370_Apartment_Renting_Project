<?php
session_start();
require_once('dbconnect.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];
$logged_in = true;

// Fetch user info
$sql = "SELECT username, email, date_of_birth, Phone_no, nid, ten_type, ren_flag FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    header("Location: logout.php");
    exit;
}
$user = mysqli_fetch_assoc($result);
$username = $user['username'];

// Update tenant type
if (isset($_POST['ten_type'])) {
    $new_type = mysqli_real_escape_string($conn, $_POST['ten_type']);
    mysqli_query($conn, "UPDATE users SET ten_type='$new_type' WHERE username='$username'");
    header("Location: profile.php");
    exit;
}

// Fetch history
$history_result = mysqli_query($conn, "SELECT * FROM history WHERE username='$username' ORDER BY date_time DESC");

// --- Requests Made --- //
$rent_requests_made = mysqli_query($conn, "
    SELECT advert_id, status 
    FROM request_apply 
    WHERE tenant_username='$username'
");

$tour_requests_made = mysqli_query($conn, "
    SELECT st.tour_id, st.advert_id, t.status, t.renter_approved
    FROM scheduled_tour st
    JOIN tour t ON st.tour_id = t.tour_id
    WHERE st.tenant_username='$username'
");

// --- Requests Received (for renters) --- //
$renter_requests = ['rental' => null, 'tour' => null];

if ($user['ren_flag'] == 1) {
    // Rental requests received
    $renter_requests['rental'] = mysqli_query($conn, "
        SELECT ra.tenant_username, ra.advert_id, ra.status
        FROM request_apply ra
        JOIN apartment a ON ra.advert_id = a.advert_id
        WHERE a.renter_username = '$username' AND ra.status = 'Pending'
    ");

    // Tour requests received
    $renter_requests['tour'] = mysqli_query($conn, "
        SELECT st.tour_id, st.tenant_username, st.advert_id, t.status, t.renter_approved
        FROM scheduled_tour st
        JOIN tour t ON st.tour_id = t.tour_id
        JOIN apartment a ON st.advert_id = a.advert_id
        WHERE a.renter_username = '$username'
    ");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <li><a href="apartments.php">Apartments</a></li>
            <li><a href="publishad.php">Publish Ad</a></li>
        </ul>
        <span class="welcome">Welcome, <?= htmlspecialchars($username) ?>!</span>
        <a href="logout.php" class="btn">Logout</a>
    </div>
</header>

<main class="profile-container">

<!-- Left Column: Profile Info -->
<div class="profile-info">
    <h2>Profile Information</h2>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($user['date_of_birth']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($user['Phone_no']) ?></p>
    <p><strong>NID:</strong> <?= htmlspecialchars($user['nid']) ?></p>
    <p><strong>Tenant Type:</strong></p>
    <form method="POST" class="tenant-form">
        <select name="ten_type" class="tenant-type">
            <option value="" disabled <?php if(empty($user['ten_type'])) echo 'selected'; ?>>Select Tenant Type</option>
            <option value="Family" <?php if($user['ten_type']=='Family') echo 'selected'; ?>>Family</option>
            <option value="Bachelor" <?php if($user['ten_type']=='Bachelor') echo 'selected'; ?>>Bachelor</option>
        </select>
        <button type="submit" class="tenant-type-button">Update</button>
    </form>
</div>

<!-- Central Column: History + Requests Made -->
<div class="profile-history">
    <div class="history-section">
        <h2>History</h2>
        <?php if(mysqli_num_rows($history_result) > 0): ?>
        <table class="history-table">
            <thead>
                <tr><th>Event</th><th>Timestamp</th></tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($history_result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['event']) ?></td>
                    <td><?= htmlspecialchars($row['date_time']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No history available.</p>
        <?php endif; ?>
    </div>

    <div class="requests-section">
        <h2>Requests You Made</h2>
        <table class="request-table">
            <thead>
                <tr><th>Type</th><th>Advert ID / Tour ID</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php
            // Rental requests made
            while($r = mysqli_fetch_assoc($rent_requests_made)) {
                echo "<tr>
                    <td>Rental</td>
                    <td>{$r['advert_id']}</td>
                    <td>{$r['status']}</td>
                    <td>-</td>
                </tr>";
            }

            // Tour requests made (tenant)
            while($t = mysqli_fetch_assoc($tour_requests_made)) {
                $action = ($t['status'] === 'Pending' && $t['renter_approved'] === 'Accepted') 
                    ? '<form method="POST" action="complete_tour.php">
                            <input type="hidden" name="tour_id" value="'.$t['tour_id'].'">
                            <input type="submit" class="tenant-type-button" value="Complete">
                      </form>'
                    : '-';
                echo "<tr>
                    <td>Tour</td>
                    <td>{$t['advert_id']}</td>
                    <td>{$t['status']}</td>
                    <td>{$action}</td>
                </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Right Column: Requests Received (Renter) -->
<div class="profile-info">
<?php if($user['ren_flag'] == 1): ?>
    <h2>Requests Received</h2>

    <!-- Rental Requests -->
    <h3>Rental Requests</h3>
    <?php if($renter_requests['rental'] && mysqli_num_rows($renter_requests['rental']) > 0): ?>
        <?php while($r = mysqli_fetch_assoc($renter_requests['rental'])): ?>
            <p>
                <?= htmlspecialchars($r['tenant_username']) ?> for Apartment #<?= $r['advert_id'] ?>
                <form method="POST" action="handle_rent_request.php" style="display:inline;">
                    <input type="hidden" name="advert_id" value="<?= $r['advert_id'] ?>">
                    <input type="hidden" name="tenant_username" value="<?= $r['tenant_username'] ?>">
                    <button type="submit" name="accept" class="accept-btn">Accept</button>
                    <button type="submit" name="reject" class="reject-btn">Reject</button>
                </form>
            </p>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No rental requests received.</p>
    <?php endif; ?>

    <!-- Tour Requests -->
    <h3>Tour Requests</h3>
    <?php if($renter_requests['tour'] && mysqli_num_rows($renter_requests['tour']) > 0): ?>
        <?php while($t = mysqli_fetch_assoc($renter_requests['tour'])): ?>
            <p>
                <?= htmlspecialchars($t['tenant_username']) ?> requested a Tour for Apartment #<?= $t['advert_id'] ?> 
                <?php if($t['renter_approved'] === 'Pending'): ?>
                    <form method="POST" action="handle_tour_request.php" style="display:inline;">
                        <input type="hidden" name="tour_id" value="<?= $t['tour_id'] ?>">
                        <button type="submit" name="accept" class="accept-btn">Accept</button>
                        <button type="submit" name="reject" class="reject-btn">Reject</button>
                    </form>
                <?php endif; ?>
            </p>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No tour requests received.</p>
    <?php endif; ?>
<?php else: ?>
    <p>No requests received (not a renter).</p>
<?php endif; ?>
</div>
</main>
</body>
</html>
