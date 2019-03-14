<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 03.03.19
 * Time: 19:32
 */


function db_conn()
{
    $server = "mysql";
    $db = "supply";
    $user = $_SESSION["user"];
    $pass = $_SESSION["pass"];

    $conn = mysqli_connect($server, $user, $pass, $db);


    if (!$conn) {
        session_unset();
        session_destroy();

        die("Connect failed :" . mysqli_connect_error());
    }

    return $conn;
}