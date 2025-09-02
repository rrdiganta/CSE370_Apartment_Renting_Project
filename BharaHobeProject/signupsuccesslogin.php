<?php
session_start();
require_once('dbconnect.php');

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) != 0) {
        
		$user = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        header("Location: index.php");
        exit;
    } 
	else {
        header("Location: loginerror.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 ">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login-style.css">
    <link rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <title>Log IN</title>
</head>
<body>
    <header>
        <div class="nav container">
            <a href="index.php" class="logo"><i class='bx  bxs-building-house'></i>BharaHobe</a>
            <a href="signup.php" class="btn">Sign Up</a>
        </div>
    </header>
    <div class="login container">
        <div class="login-container">
            <p style="color: green;">Signed up succesfully!</p>
			<h2>Login To Continue</h2>
            <form action="" method="post">
                <span for="email">Enter your email address</span>
                <input type="email" id="email" name="email" required>
                <span>Enter your password</span>
                <input type="password" id="password" name="password" required>
                <a href="#">Forgot Password?</a>
                <input type="submit" value="Log In" class="buttom">
            </form>
            <a href="signup.php" class="btn">Sign up now</a>
        </div>
        <div class="login-image">
            <img src="img/login.jpg" alt="">
        </div>
    </div>
</body>
</html>