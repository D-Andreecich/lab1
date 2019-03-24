<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 14.03.19
 * Time: 20:33
 */

if (isset($_POST["create_contract"])) {
    $supplier_id = $_POST["supplier_id"];
    $contract_note = $_POST["contract_note"];

    $sql = "CALL sp_contract_ops('i', 0, CURRENT_TIMESTAMP(), {$supplier_id}, '{$contract_note}')";
    mysqli_query($conn, $sql);

    header("location: index.php");
}

if (isset($_POST["delete_contract"])) {
    $contract_number = $_POST["contract_number"];

    $sql = "CALL sp_contract_ops('d', '{$contract_number}', CURRENT_TIMESTAMP(), 0, '')";
    mysqli_query($conn, $sql);

    header("location: index.php");
}

if (isset($_POST["update_contract"])) {
    $contract_number = $_POST["contract_number"];
    $supplier_id = $_POST["supplier_id"];
    $contract_note = $_POST["contract_note"];

    $sql = "CALL sp_contract_ops('u', {$contract_number}, CURRENT_TIMESTAMP(), {$supplier_id}, '{$contract_note}')";
    mysqli_query($conn, $sql);

    header("location: index.php");
}

if (isset($_POST["add_product"])) {
    $supplied_product = $_POST["supplied_product"];
    $supplied_amount = $_POST["supplied_amount"];
    $supplied_cost = $_POST["supplied_cost"];


    if (!empty($supplied_product) && !empty($supplied_amount) && !empty($supplied_cost)) {
        if (is_numeric($supplied_amount) && is_numeric($supplied_cost)) {
            if ($supplied_amount > 0 && $supplied_cost > 0) {
                $_SESSION["supplied_products"][$supplied_product] = array("amount" => $supplied_amount, "cost" => $supplied_cost);
            }
        }
    }

    header("location: index.php");
}

if (isset($_GET["supplied"]) && $_GET["supplied"] == "remove") {
    $supplied_product = $_GET["product"];

    unset($_SESSION["supplied_products"][$supplied_product]);

    header("location: index.php");
}

if (isset($_POST["save_products"])) {
    $contract_number = $_POST["contract_number"];

    mysqli_query($conn, "SET AUTOCOMMIT = 0");
    mysqli_query($conn, "START TRANSACTION");

    $failed = false;

    foreach ($_SESSION["supplied_products"] as $key => $value) {
        $amount = $value["amount"];
        $cost = $value["cost"];

        $result = mysqli_query($conn, "INSERT INTO supplied (contract_number, supplied_product, supplied_amount, supplied_cost) values ({$contract_number},'{$key}', {$amount}, {$cost})");
        if (!$result) {
            $failed = true;
            mysqli_query($conn, "ROLLBACK");
            break;
        }
    }

    if (!$failed) {
        mysqli_query($conn, "COMMIT");
    }

    mysqli_query($conn, "SET AUTOCOMMIT = 1");

    $_SESSION["supplied_products"] = null;

    header("location: index.php");
}

if (isset($_GET["action"]) && $_GET["action"] == "export") {
    $filename = "report_contracts_" . date("Ymd") . ".xls";

    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: application/vnd.ms-excel");

    $flag = false;
    $result = mysqli_query($conn, "CALL sp_contract_total('2018-01-01', CURRENT_TIMESTAMP())");
    while ($row = mysqli_fetch_assoc($result)) {
        if (!$flag) {
            echo implode("\t", array_keys($row)) . "\r\n";
            $flag = true;
        }

        array_walk($row, __NAMESPACE__ . "\cleanData");
        echo implode("\t", array_values($row)) . "\r\n";
    }

    exit;
}

function cleanData(&$str)
{
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);

    if (strstr($str, '"')) {
        $str = '"' . str_replace('"', '""', $str) . '"';
    }
}

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
    if ($sort) {
        $sql = $foreign = getForeignByTable($_GET['table'], $conn, $sql);

        if (isset($_GET['sort']) && isset($_GET['direction'])) {
            $sql .= " order by {$_GET['sort']} {$_GET['direction']}";
        }
    }

    var_dump($sql);

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