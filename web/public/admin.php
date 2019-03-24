<?php
if (!isset($_SESSION['user'])) {
    header("location: login.php");

}
$sort = [
    'asc' => "&#9650;",
    'desc' => "&#9660;",
];
?>

    <h3>Tables</h3>
    <p>
        <?php
        $urlBack = isset($_GET['table']) && isset($_GET['action']) ? "?table={$_GET['table']}" : '';

        if (isset($_GET['table'])) {
            $sql = "SHOW COLUMNS FROM {$_GET['table']}";
            $columns = mysqli_query($conn, $sql);
            $arrColumns = [];
            $idColumn = 'id';
            ?>
            <a href="index.php<?= $urlBack ?>">Back</a>

            <?php
        } else {
            $sql = "SHOW TABLES";
            $result = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <a href="index.php?table=<?= current($row) ?>"><?= current($row) ?></a>
                <br>
                <?php
            }
        }
        ?>
    </p>


<?php
if (isset($_GET['action']) && ($_GET['action'] == 'create' || $_GET['action'] == 'update') || $_GET['action'] == 'delete') {

    ?>
    <form action="index.php?table=<?= $_GET['table'] ?>" method="post">
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
            <a href="index.php?action=create&table=<?= $_GET['table'] ?>">Create
                entry</a>
        </p>
        <table>
            <tr>
                <?php
                while ($column = mysqli_fetch_assoc($columns)) {
                    $arrColumns [] = $column['Field'];
                    ?>
                    <th>
                        <a href="index.php?table=<?= $_GET['table'] ?>&sort=<?= $column['Field'] ?>&direction=<?= $_GET['direction'] === 'desc' ? 'asc' : 'desc' ?>">
                            <?= $_GET['sort'] === $column['Field'] ? $sort[$_GET['direction']] : "&#9658;" ?>
                            <?= variableToName($column['Field']) ?>
                        </a>
                    </th>
                    <?php
                }
                $idColumn = isset($arrColumns[0]) ? $arrColumns[0] : $idColumn;
                ?>
                <th>Action</th>
            </tr>

            <?php

            $sql = "SELECT * from {$_GET['table']}";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <?php
                    foreach ($arrColumns

                             as $column) {
                        $foreign = getLinkForeign($_GET['table'], $conn);
                        ?>
                        <td>
                            <?php
                            if ($foreign && $foreign['name_column'] == $column) {
                                ?>
                                <a href="index.php?<?= "table={$foreign['table']}&where_column={$foreign['where']}&where={$row[$column]}" ?>"><?= $row[$column] ?></a>
                                <?php
                            } else {
                                echo $row[$column];
                            }
                            ?>
                        </td>

                        <?php
                    }
                    ?>
                    <td>
                        <a href="index.php?action=update&id_column=<?= $idColumn ?>&<?= $idColumn ?>=<?= $row[$idColumn] ?>&table=<?= $_GET['table'] ?>">Update</a>
                        <a href="index.php?action=delete&id_column=<?= $idColumn ?>&<?= $idColumn ?>=<?= $row[$idColumn] ?>&table=<?= $_GET['table'] ?>">Delete</a>
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