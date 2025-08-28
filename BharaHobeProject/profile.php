<?php
session_start();
require_once('dbconnect.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];

$sql = "SELECT username, email, date_of_birth, Phone_no, nid, ten_type FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: logout.php");
    exit;
}

$user = mysqli_fetch_assoc($result);
$username = $user['username'];
if (isset($_POST['ten_type'])) {
    $new_type = mysqli_real_escape_string($conn, $_POST['ten_type']);
    $update_sql = "UPDATE users SET ten_type = '$new_type' WHERE username = '$username'";
    mysqli_query($conn, $update_sql);

    header("Location: profile.php");
    exit;
}

$history_sql = "SELECT * FROM history WHERE username = '$username' ORDER BY date_time DESC";
$history_result = mysqli_query($conn, $history_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 ">
    <title>Profile - BharaHobe</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
</head>
<body class="profile-page">
<header>
    <div class="nav container">
        <a href="index.php" class="logo"><i class='bx bxs-building-house'></i>BharaHobe</a>
        <input type="checkbox" name="" id="menu">
        <label for="menu"><i class='bx bxs-menu' id="menu-icon"></i></label>
        <ul class="navbar">
            <li><a href="index.php">Home</a></li>
            <li><a href="#Services">Services</a></li>
            <li><a href="#Apartments">Apartments</a></li>
            <li><a href="publishad.php">Publish Ad</a></li>
        </ul>
        <?php if (isset($_SESSION['email'])): ?>
            <span class="welcome">Welcome, <?= htmlspecialchars($username) ?>!</span>
            <a href="profile.php" class="btn">Profile</a>
            <a href="logout.php" class="btn">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn">Log In</a>
            <a href="signup.php" class="btn">Sign Up</a>
        <?php endif; ?>
    </div>
</header>

<main class="profile-container">
    <!-- Profile Info -->
    <div class="profile-info">
        <h2>Profile Information</h2>
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($user['date_of_birth']); ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($user['Phone_no']); ?></p>
        <p><strong>NID:</strong> <?= htmlspecialchars($user['nid']); ?></p>
		<p><strong>Tenant Type:</strong></p>

    <?php if ($_SESSION['email'] === $user['email']): ?>
        <!-- For logged in users-->
        <form method="POST" action="" class="tenant-form">
            <select name="ten_type" class="tenant-type">
				<option value="" disabled <?php if (empty($user['ten_type'])) echo 'selected'; ?>>Select Tenant Type</option>
				<option value="Family" <?php if ($user['ten_type'] == 'Family') echo 'selected'; ?>>Family</option>
				<option value="Bachelor" <?php if ($user['ten_type'] == 'Bachelor') echo 'selected'; ?>>Bachelor</option>
			</select>
            <button type="submit" name="update_ten_type" class="tenant-type-button">Update</button>
        </form>
    <?php else: ?>
        <!-- For viewers -->
        <p><?= htmlspecialchars($user['ten_type'] ?? 'Not set'); ?></p>
    <?php endif; ?>
    </div>

    <!-- History -->
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

    <!-- Right Column -->
    <div class="profile-empty"></div>
</main>
</body>
</html>