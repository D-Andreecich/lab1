<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 03.03.19
 * Time: 19:32
 */
session_start();

session_unset();
session_destroy();

header("location: login.php");