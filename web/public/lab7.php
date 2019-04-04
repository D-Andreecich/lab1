<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 03.03.19
 * Time: 19:32
 */

session_start();
require_once 'connect.php';
require_once 'helpers.php';
$conn = NULL;

$dbName = "supply";
$conn = db_conn($dbName);
include 'action.php';

if (isset($_SESSION["user"])) {
    
} else {
    header('location: lab7/login.php');
}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Supply</title>
    </head>
    <body>
    <div>
        <a href="/index.php">labs</a>
        <hr>
    </div>
    <p>
        <b>User:</b>
        <i><?= $_SESSION['user'] ?></i> | <a href="/lab7/logout.php">Logout</a>
        <?php
        if ($_SESSION['user']) {
            include 'lab7/adminka.php';
        }
        ?>
    </p>
    </body>
    </html>
<?php
mysqli_close($conn);