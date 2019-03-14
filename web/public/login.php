<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 03.03.19
 * Time: 19:32
 */
session_start();

if (isset($_POST['login'])) {
    session_unset();

    $_SESSION['user'] = $_POST['user'];
    $_SESSION['pass'] = $_POST['pass'];

    header("location: index.php");
} else {
    if (isset($_SESSION['user'])) {
        header("location: index.php");
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
</head>
<body>
<h3>Supply Application login</h3>
<form action="login.php" method="post">
    <p>
        <b>User name:</b>
    </p>
    <p>
        <input type="text" name="user" required/>
    </p>
    <p>
        <b>Password:</b>
    </p>
    <p>
        <input type="password" name="pass" required/>
    </p>
    <p>
        <input type="submit" name="login" value="Login"/>
    </p>
</form>
</body>
</html>