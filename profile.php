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
$stmt = $con->prepare('SELECT password, username, email, phone, address FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $username, $email, $phone, $address);
$stmt->fetch();
$stmt->close();
if (!empty($_POST['old_password'])) {
	if (!password_verify($_POST['old_password'], $password)) {
		exit('incorrect password!');
	}
	if (!empty($_POST['new_username'])) {
		$change_user = $con->prepare('UPDATE `accounts` SET `username` = (?) WHERE (?)');
		$change_user->bind_param('ss', $_POST['new_username'], $_SESSION['id']);
		$change_user->execute();
		$change_user->close();
	}
	if (!empty($_POST['new_password'])) {
		$change_user = $con->prepare('UPDATE `accounts` SET `password` = (?) WHERE (?)');
		$change_user->bind_param('ss', password_hash($_POST['new_password'], PASSWORD_DEFAULT), $_SESSION['id']);
		$change_user->execute();
		$change_user->close();
	}
	if (!empty($_POST['new_email'])) {
		$change_user = $con->prepare('UPDATE `accounts` SET `email` = (?) WHERE (?)');
		$change_user->bind_param('ss', $_POST['new_email'], $_SESSION['id']);
		$change_user->execute();
		$change_user->close();
	}
	if (!empty($_POST['new_phone'])) {
		$change_user = $con->prepare('UPDATE `accounts` SET `phone` = (?) WHERE (?)');
		$change_user->bind_param('ss', $_POST['new_phone'], $_SESSION['id']);
		$change_user->execute();
		$change_user->close();
	}
	if (!empty($_POST['new_address'])) {
		$change_user = $con->prepare('UPDATE `accounts` SET `address` = (?) WHERE (?)');
		$change_user->bind_param('ss', $_POST['new_address'], $_SESSION['id']);
		$change_user->execute();
		$change_user->close();
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
						<td>Email:</td>
						<td><?=$email?></td>
					</tr>
					<tr>
						<td>Phone:</td>
						<td><?=$phone?></td>
					</tr>
					<tr>
						<td>Address:</td>
						<td><?=$address?></td>
					</tr>
				</table>
				<form action = "profile.php" method = "post">
					<table>
						<tr>
							<td>new username:</td>
							<td> <input type="text" name = "new_username" placeholder="new username" id="username"> </td>
						</tr>
						<tr>
							<td>new password: </td>
							<td> <input type = "text" name = "new_password" placeholder = "new password" id = "password"> </td> 
						</tr>
						<tr>
							<td>new email: </td>
							<td> <input type = "text" name = "new_email" placeholder = "new email" id = "email"> </td> 
						</tr>
						<tr>
							<td>new phone: </td>
							<td> <input type = "text" name = "new_phone" placeholder = "new phone" id = "phone"> </td> 
						</tr>
						<tr>
							<td>new address: </td>
							<td> <input type = "text" name = "new_address" placeholder = "new address" id = "address"> </td> 
						</tr>
						<tr>
							<td>old password (required): </td>
							<td> <input type = "text" name = "old_password" placeholder = "old password" id = "password" required> </td> 
							<td> <input type = "submit" value = "update profile info"> </td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</body>
</html>