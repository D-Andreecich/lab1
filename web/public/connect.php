<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 03.03.19
 * Time: 19:32
 * @param string $dbName
 * @param string $user
 * @param string $pass
 * @return mysqli
 */
function db_conn(string $dbName = "supply", string $user = "root", string $pass = "root")
{
    $server = "mysql";
    $conn = mysqli_connect($server, $user, $pass, $dbName);


    if (!$conn) {
        session_unset();
        session_destroy();

        die("Connect failed :" . mysqli_connect_error());
    }

    return $conn;
}