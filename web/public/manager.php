<?php
if (!isset($_SESSION['user'])) {
    header("location: login.php");

}
?>

    <h3>Contracts</h3>
    <p>
        <?php
        if (isset($_GET['action']) && ($_GET['action'] == 'create' || $_GET['action'] == 'update') || $_GET['action'] == 'delete') {

            ?>
            <a href="index.php">Back</a>

            <?php
        } else {
            ?>
            <a href="index.php?action=create">New contract</a>
            <a href="index.php?action=export">Export data</a>
            <?php
        }
        ?>
    </p>


<?php
if (isset($_GET['action']) && ($_GET['action'] == 'create' || $_GET['action'] == 'update') || $_GET['action'] == 'delete') {

    ?>
    <form action="index.php" method="post">
        <input type="hidden" value="<?= $_GET['id'] ?>" name="contract_number"/>
        <?php
        if ($_GET['action'] == 'create' || $_GET['action'] == 'update') {
            ?>
            <p>
                <b>Supplier</b>
            </p>
            <p>
                <select name="supplier_id">
                    <?php
                    $sql = "SELECT * FROM supplier_info";
                    $result = mysqli_query($conn, $sql);
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <option value="<?= $row['supplier_id'] ?>"><?= $row['Info'] ?></option>
                        <?php
                    }
                    ?>
                </select>
            </p>
            <p>
                <b>Note</b>
            </p>
            <p>
                <?php
                if (isset($_GET['action']) && $_GET['action'] == 'update') {
                    $contract_number = $_GET['id'];

                    $sql = "SELECT contract_note FROM contract where contract_number = {$contract_number}";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                }
                ?>
                <textarea name="contract_note" cols="50" rows="5"><?= $row['contract_note'] ?></textarea>
            </p>
            <p>
                <?php
                if (isset($_GET['action']) && $_GET['action'] == 'create') {
                    ?>
                    <input type="submit" name="create_contract" value="Save"/>
                    <?php
                } else if (isset($_GET['action']) && $_GET['action'] == 'update') {
                    ?>
                    <input type="submit" name="update_contract" value="Save"/>
                    <?php
                }
                ?>
            </p>
            <?php
        } else if ($_GET['action'] == 'delete') {
            ?>
            <b>Delete the contract#<?= $_GET['id'] ?>?</b>
            <p>
                <input type="submit" name="delete_contract" value="Continue"/>
            </p>
            <?php
        }
        ?>
    </form>

    <?php
} else {
    ?>
    <table>
        <tr>
            <th>Contract number</th>
            <th>Contract date</th>
            <th>Supplier</th>
            <th>Note</th>
            <th>Action</th>
        </tr>

        <?php

        $sql = "SELECT contract_supplier.*, (SELECT contract_note FROM contract where contract_number  = contract_supplier.contract_number) as note from contract_supplier";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><a href="index.php?action=info&id=<?= $row['contract_number'] ?>"><?= $row['contract_number'] ?></a>
                </td>
                <td><?= $row['contract_date'] ?></td>
                <td><?= $row['Supplier'] ?></td>
                <td><?= $row['note'] ?></td>
                <td>
                    <a href="index.php?action=update&id=<?= $row['contract_number'] ?>">Update</a>
                    <a href="index.php?action=delete&id=<?= $row['contract_number'] ?>">Delete</a>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}

if (isset($_GET['action']) && $_GET['action'] == "info") {
    $contract_number = $_GET['id'];
    ?>
    <h3>Supplied product by contract# <?= $contract_number ?></h3>
    <p>
        <a href="index.php">Hide</a>
    </p>
    <?php
    $sql = "SELECT supplied_product, supplied_amount, supplied_cost FROM supplied WHERE contract_number = {$contract_number}";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        ?>
        <table border="1">
        <tr>
            <th>Product</th>
            <th>Amount</th>
            <th>Cost</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?= $row['supplier_product'] ?></td>
                <td><?= $row['supplier_amount'] ?></td>
                <td><?= $row['supplier_cost'] ?></td>
            </tr>
            <?php
        }
    } else {
        echo "Contract is empty";
    }
    ?>
    </table>
    <?php
}
?>