<?php
require_once 'includes/db.php';

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['password'];

$result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    header('Location: home.php');
} else {
    header('Location: index.php?error=Identifiants incorrects');
}
exit;