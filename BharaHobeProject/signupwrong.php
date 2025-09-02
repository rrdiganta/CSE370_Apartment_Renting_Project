<?php
require_once('dbconnect.php');

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
    
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $dob      = $_POST['dob'];
    $phone    = $_POST['phone'];
    $nid      = $_POST['nid'];
    $password = $_POST['password'];

    $check_sql = "SELECT * FROM users WHERE email = '$email' AND nid = '$nid'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        header("Location: signup.php?error=Email already exists");
    } 
	else {
        $insert_sql = "INSERT INTO users (username, email, date_of_birth, nid, phone_no, password, ten_flag, ten_type, ren_flag) 
                VALUES ('$username', '$email', '$dob', '$nid', '$phone', '$password', 1, NULL, 0);";
        $result = mysqli_query($conn, $insert_sql);

        if ($result) {
            mysqli_query($conn, "INSERT INTO history (username, event) VALUES ('$username', 'Joined BharaHobe')");
			header("Location: signupsuccesslogin.php");
        } 
		else {
            header("Location: signupwrong.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 ">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/login-style.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <title>Sign Up</title>
</head>
<body>
    <header>
        <div class="nav container">
            <a href="index.php" class="logo"><i class='bx  bxs-building-house'></i>BharaHobe</a>
            <a href="login.php" class="btn">Log in</a>
        </div>
    </header>
    <div class="login container">
        <div class="login-container">
            <h2>Welcome, let's get started</h2>
            <p style="color: red;">Something went wrong</p>
            <form action="" method="post">
                <span>Username</span>
                <input type="text" id="username" name="username" placeholder="@johndoe" required>
                <span>Enter your email address</span>
                <input type="email" id="email" name="email" placeholder="johndoe@gmail.com" required>
				<span>Date of birth</span>
				<input type="date" id="dob" name="dob" required>
                <span>Phone</span>
                <input type="tel" id="tel" name="phone" placeholder="+8801XXXXXXXXX" required>
                <span>NID</span>
                <input type="number" class="no-spinner" id="number" name="nid" placeholder="Enter your NID" required>
                <span>Enter your password</span>
                <input type="password" id="password" name="password" placeholder="At least 8 characters required" required>
                <input type="submit" value="Sign Up" class="buttom">
                <!-- <a href="login.php">Already have an account?</a> -->
            </form> 
        </div>
        <div class="login-image">
            <img src="img/signup.jpg" alt="">
        </div>
    </div>
</body>
</html>
