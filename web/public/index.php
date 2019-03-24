<?php
session_start();


require_once 'connect.php';
require_once 'helpers.php';

$conn = NULL;

if (isset($_SESSION["user"])) {
    $conn = db_conn();
    include 'action.php';
} else {
    header('location: login.php');
}

?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Supply</title>
    </head>
    <body>
    <p>
        <b>User:</b>
        <i><?= $_SESSION['user'] ?></i> | <a href="logout.php">Logout</a>
        <?php
        if ($_SESSION['user'] == "manager") {
            include 'manager.php';
        }
        if ($_SESSION['user'] == "storekeeper") {
            include 'storekeeper.php';
        }
        if ($_SESSION['user'] == "admin") {
            include 'admin.php';
        }
        ?>
    </p>
    </body>
    </html>
<?php
mysqli_close($conn);
