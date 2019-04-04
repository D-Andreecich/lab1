<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 02.04.19
 * Time: 22:34
 */

session_start();
session_unset();
session_destroy();
header("location: /lab7/login.php");