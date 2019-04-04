<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 02.04.19
 * Time: 22:38
 */

if (!isset($_SESSION['user'])) {
    header("location: lab7/login.php");
} else {
    $permissionTables = $_SESSION['user_data']['exclude_tables'] ?: "''"; //get from table users  // exclude_tables
    $permissionActions = $_SESSION['user_data']['actions'] ? //get from table users //actions
        $_SESSION['user_data']['actions'] = (is_array($_SESSION['user_data']['actions']) ? $_SESSION['user_data']['actions'] : json_decode($_SESSION['user_data']['actions'])) : [];
}
$sort = [
    'asc' => "&#9650;",
    'desc' => "&#9660;",
];
?>

    <h3>Adminka <a href="lab7/createUser.php">Create User</a></h3>

    <p>
<?php
$urlBack = isset($_GET['table']) && isset($_GET['action']) ? "?table={$_GET['table']}" : '';
if (isset($_GET['table'])) {
    $sql = "SHOW COLUMNS FROM {$_GET['table']}";
    $columns = mysqli_query($conn, $sql);
    $arrColumns = [];
    $idColumn = 'id';
    ?>
    <a href="lab7.php<?= $urlBack ?>">Back</a>

    <?php
} else {
    $sql = "SHOW TABLES WHERE Tables_in_{$dbName} NOT IN ({$permissionTables})";
    $result = querySql($sql, $conn);
    if ($result->num_rows) {
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <a href="lab7.php?table=<?= current($row) ?>"><?= current($row) ?></a>
            <br>
            <?php
        }
    } else {
        ?>
        <p>Empty data for <i><?= $_SESSION['user'] ?></i></p>
        <?php
    }
}
?>
    </p>


<?php
if (isset($_GET['action']) && ($_GET['action'] == 'create' || $_GET['action'] == 'update') || $_GET['action'] == 'delete') {
    ?>
    <form action="lab7.php?table=<?= $_GET['table'] ?>" method="post">
        <input type="hidden" value="<?= $_GET['id_column'] ?>" name="id_column"/>
        <input type="hidden" value="<?= $_GET[$_GET['id_column']] ?>" name="<?= $_GET['id_column'] ?>"/>
        <?php
        if ($_GET['action'] == 'create' || $_GET['action'] == 'update') {
            ?>
            <p>
                <b><?= variableToName($_GET['table']) ?></b>
            </p>
            <?php
            if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['id_column'])) {
                $idColumn = $_GET['id_column'];
                $$idColumn = $_GET[$idColumn];
                $sql = "SELECT * FROM {$_GET['table']} where {$idColumn} = {$$idColumn}";
                $result = mysqli_query($conn, $sql);
                $dataEntry = mysqli_fetch_array($result);
            }
            ?>
            <table border="1">
                <tr>
                    <?php
                    while ($column = mysqli_fetch_assoc($columns)) {
                        if ($_GET['action'] == 'create' && $column['Extra'] === 'auto_increment')
                            continue;
                        $arrColumns[] = $column;
                        ?>
                        <th><?= variableToName($column['Field']) ?></th>
                        <?php
                    }
                    $idColumn = isset($arrColumns[0]) ? $arrColumns[0]['Field'] : $idColumn;
                    ?>
                </tr>
                <tr>
                    <?php
                    foreach ($arrColumns as $column) {
                        $column['Value'] = $dataEntry[$column['Field']];
                        ?>
                        <td>
                            <?= generatorInput(enumTypeDbForPhp($column)) ?>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
            </table>
            <p>
                <input type="submit" name="<?= $_GET['action'] ?>_entry" value="<?= $_GET['action'] ?>"/>
            </p>
            <?php
        } else if ($_GET['action'] == 'delete') {
            ?>
            <b>Delete entry#<?= $_GET[$_GET['id_column']] ?> from <?= $_GET['table'] ?>?</b>
            <p>
                <input type="submit" name="delete_entry" value="Continue"/>
            </p>
            <?php
        }
        ?>
    </form>

    <?php
} else if (isset($_GET['table'])) {
    if ($_GET['table'] && $ifExist = mysqli_query($conn, "SHOW TABLES FROM supply LIKE '{$_GET['table']}'")) {
        ?>
        <p>
            <b><?= variableToName($_GET['table']) ?></b>
            <?php
            if (in_array('insert', $permissionActions)) {
                ?>
                <a href="lab7.php?action=create&table=<?= $_GET['table'] ?>">
                    Create entry
                </a>
                <?php
            }
            ?>
        </p>
        <table>
            <tr>
                <?php
                while ($column = mysqli_fetch_assoc($columns)) {
                    $arrColumns [] = $column['Field'];
                    ?>
                    <th>
                        <a href="lab7.php?table=<?= $_GET['table'] ?>&sort=<?= $column['Field'] ?>&direction=<?= $_GET['direction'] === 'desc' ? 'asc' : 'desc' ?>">
                            <?= $_GET['sort'] === $column['Field'] ? $sort[$_GET['direction']] : "&#9658;" ?>
                            <?= variableToName($column['Field']) ?>
                        </a>
                    </th>
                    <?php
                }
                $idColumn = isset($arrColumns[0]) ? $arrColumns[0] : $idColumn;
                ?>
                <th>Action <a href="lab7.php?table=<?= $_GET['table'] ?>"> reset</a></th>
            </tr>

            <?php
            $sql = "SELECT * from {$_GET['table']}";
            $result = querySql($sql, $conn, true);
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <?php
                    foreach ($arrColumns as $column) {
                        $foreign = getLinkForeign($_GET['table'], $conn);
                        ?>
                        <td>
                            <?php
                            if ($foreign && $foreign['name_column'] == $column) {
                                ?>
                                <a href="lab7.php?<?= "table={$foreign['table']}&where_column={$foreign['where']}&where={$row[$column]}" ?>"><?= $row[$column] ?></a>
                                <?php
                            } else {
                                ?>
                                <a href="lab7.php?<?= "table={$_GET['table']}&where_column={$column}&where={$row[$column]}" ?>">&#x268C;</a>
                                <?php
                                echo $row[$column];
                            }
                            ?>
                        </td>

                        <?php
                    }
                    ?>
                    <td>
                        <?php
                        if (in_array('update', $permissionActions)) {
                            ?>
                            <a href="lab7.php?action=update&id_column=<?= $idColumn ?>&<?= $idColumn ?>=<?= $row[$idColumn] ?>&table=<?= $_GET['table'] ?>">Update</a>
                            <?php
                        }
                        if (in_array('delete', $permissionActions)) {
                            ?>
                            <a href="lab7.php?action=delete&id_column=<?= $idColumn ?>&<?= $idColumn ?>=<?= $row[$idColumn] ?>&table=<?= $_GET['table'] ?>">Delete</a>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    } else {
        ?>
        <b>No table <?= $_GET['table'] ?>!</b>
        <?php
    }
}