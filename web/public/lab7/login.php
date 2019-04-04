<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 02.04.19
 * Time: 22:33
 */

require_once '../connect.php';
$dbName = "supply";
$conn = db_conn($dbName);

$errors = [];

session_start();
if (isset($_SESSION['user'])) {
    header("location: /lab7.php");
} elseif (isset($_POST['submit_login'])) {
    session_unset();
    $sql = "SELECT * FROM users WHERE login = '{$_POST['login']}'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        $errors[] = "No valid data!";
    } else if (!password_verify($_POST['password'], $row['password'])) {
        $errors[] = "Wrong password!";
    } else if (password_verify($_POST['password'], $row['password'])) {
        $_SESSION['user'] = $_POST['login'];
        $_SESSION['user_data'] = $row;
        header("location: /lab7.php");
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
<div>
    <a href="/lab7.php">back</a>
    <a href="/index.php">labs</a>
    <hr>
</div>
<h3>Supply Application login</h3>
<?php
foreach ($errors as $key => $value) {
    echo "<div style='color: red'> $value </div><br/>";
}
?>
<form action="/lab7/login.php" method="post">
    <p>
        <b>User name:</b>
    </p>
    <p>
        <input type="text" name="login" required/>
    </p>
    <p>
        <b>Password:</b>
    </p>
    <p>
        <input type="password" name="password" required/>
    </p>
    <p>
        <input type="submit" name="submit_login" value="Login"/>
    </p>
</form>
</body>
</html>