<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 14.03.19
 * Time: 20:33
 */

require_once 'connect.php';
require_once 'helpers.php';
$conn = db_conn();


if (isset($_POST["create_entry"]) && isset($_GET['table'])) {
    $sql = "SHOW COLUMNS FROM {$_GET['table']}";
    $columns = mysqli_query($conn, $sql);

    $action = "insert into {$_GET['table']} ";
    $insertField = '';
    $insertValues = '';

    while ($row = mysqli_fetch_assoc($columns)) {
        if ($_POST[$row['Field']]) {
            $insertField .= "{$row['Field']}, ";
            $insertValues .= "'{$_POST[$row['Field']]}', ";
        }
    }
    $insertField = rtrim($insertField, ', ');
    $insertValues = rtrim($insertValues, ', ');

    $sql = "$action ({$insertField}) values ({$insertValues})";

    querySql($sql, $conn);
}

if (isset($_POST["update_entry"]) && isset($_GET['table'])) {
    $sql = "SHOW COLUMNS FROM {$_GET['table']}";
    $columns = mysqli_query($conn, $sql);


    $action = "update {$_GET['table']} set";
    $insertValues = '';

    while ($row = mysqli_fetch_assoc($columns)) {
        if (isset($_POST[$row['Field']])) {
            $insertValues .= "{$row['Field']}='{$_POST[$row['Field']]}', ";
        }
    }
    $insertValues = rtrim($insertValues, ', ');

    $sql = "$action {$insertValues} where {$_POST['id_column']}={$_POST[$_POST['id_column']]}";

    querySql($sql, $conn);
}

if (isset($_POST["delete_entry"]) && isset($_GET['table'])) {
    $sql = "DELETE FROM {$_GET['table']} where {$_POST['id_column']}={$_POST[$_POST['id_column']]}";

    querySql($sql, $conn);
}

function querySql(string $sql, $conn, bool $sort = false)
{
    if (isset($_GET['where']) && isset($_GET['where_column'])) {
        $sql .= " where {$_GET['where_column']} = '{$_GET['where']}'";
    }
    
    if ($sort) {
        if (isset($_GET['sort']) && isset($_GET['direction'])) {
            $sql .= " order by {$_GET['sort']} {$_GET['direction']}";
        }
    }

    $result = mysqli_query($conn, $sql);

    if (!mysqli_errno($conn)) {
        if (!$sort)
            echo "Status: Successful!";
        return $result;
    } else {
        echo 'Status: Error!' . '<br/>';
        echo 'SQL : (' . $sql . ')' . '<br/>';
        echo 'Error message: ' . mysqli_error($conn);
    }
}

function getForeignByTable(string $tableName, $conn, string $select)
{
    $sql = "SELECT
  TABLE_NAME,
  COLUMN_NAME,
  REFERENCED_TABLE_NAME,
  REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_NAME LIKE '{$tableName}%'
AND NOT ISNULL(REFERENCED_TABLE_NAME)";

    $result = mysqli_query($conn, $sql);


    if (!mysqli_errno($conn)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $select .= " left join {$row['REFERENCED_TABLE_NAME']} on {$row['TABLE_NAME']}.{$row['COLUMN_NAME']} = {$row['REFERENCED_TABLE_NAME']}.{$row['REFERENCED_COLUMN_NAME']}";

        }

        return $select;
    } else {
        echo 'Status: Error!' . '<br/>';
        echo 'SQL : (' . $sql . ')' . '<br/>';
        echo 'Error message: ' . mysqli_error($conn);
    }
}

function getLinkForeign(string $tableName, $conn)
{
    $sql = "SELECT
  TABLE_NAME,
  COLUMN_NAME,
  REFERENCED_TABLE_NAME,
  REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_NAME LIKE '{$tableName}%'
AND NOT ISNULL(REFERENCED_TABLE_NAME)";

    $result = mysqli_query($conn, $sql);


    if (!mysqli_errno($conn)) {
        if ($row = mysqli_fetch_assoc($result)) {
            return ['table' => $row['REFERENCED_TABLE_NAME'], 'name_column' => $row['COLUMN_NAME'], 'where' => $row['REFERENCED_COLUMN_NAME']];
        } else {
            return false;
        }
    } else {
        echo 'Status: Error!' . '<br/>';
        echo 'SQL : (' . $sql . ')' . '<br/>';
        echo 'Error message: ' . mysqli_error($conn);
    }
}