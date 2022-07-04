<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}


$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// We don't have the password or email info stored in sessions so instead we can get the results from the database.
$stmt = $con->prepare('SELECT password, username, email FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $username, $email);
$stmt->fetch();
$stmt->close();
if (!empty($_POST['old_password'])) {
	if (!password_verify($_POST['old_password'], $password)) {
		exit('incorrect password!');
	}
	if (!empty($_POST['new_username'])) {
		if ($con->query(sprintf('UPDATE `accounts` SET `username` = "%s" WHERE %d', $_POST['new_username'], $_SESSION['id'])) === TRUE) {
			echo "username updated successfully";
		}
	}
	if (!empty($_POST['new_password'])) {
		if ($con->query(sprintf('UPDATE `accounts` SET `password` = "%s" WHERE %d', password_hash($_POST['new_password'], PASSWORD_DEFAULT), $_SESSION['id'])) === TRUE) {
			echo "password updated successfully";
		}
	}
	header('Location: profile.php');
	exit;
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Website Title</h1>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Profile Page</h2>
			<div>
				<p>Your account details are below:</p>
				<table>
					<tr>
						<td>Username:</td>
						<td><?=$username?></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><?=$password?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?=$email?></td>
					</tr>
				</table>
				<form action = "profile.php" method = "post">
					<table>
						<tr>
							<td>new username:</td>
							<td> <input type="text" name = "new_username" placeholder="new username" id="username"> </td>
							<td> <input type = "submit" value = "update profile info"> </td>
						</tr>
						<tr>
							<td>old password: </td>
							<td> <input type = "text" name = "old_password" placeholder = "old password" id = "password" required> </td> 
						</tr>
						<tr>
							<td>new password: </td>
							<td> <input type = "text" name = "new_password" placeholder = "new password" id = "password"> </td> 
						</tr>
					</table>
				</form>	
			</div>
		</div>
	</body>
</html>