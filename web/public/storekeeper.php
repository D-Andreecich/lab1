<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 13.03.19
 * Time: 21:44
 */

if (!isset($_SESSION["user"])) {
    header("location: login.php");
}

if (!isset($_SESSION["supplied_products"])) {
    $_SESSION["supplied_products"] = array();
}
?>
    <h3>Supplied products</h3>

<?php
$sql = "SELECT * FROM contract_supplier where contract_number NOT IN (select contract_number from supplied)";
$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) > 0) {

    if (sizeof($_SESSION['supplied_products']) > 0) {
        ?>
        <form action="index.php" method="post">
            <p>
                <b>by contract</b>
                <select name="contract_number">
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <option value="<?= $row['contract_number'] ?>">
                            <?= $row['contract_number'] . ' - ' . $row['Supplier'] . "( " . $row['contract_date'] . ")" ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </p>
            <table border="1">
                <tr>
                    <th>Product</th>
                    <th>Amount</th>
                    <th>Cost</th>
                    <th>Action</th>
                </tr>
                <?php
                foreach ($_SESSION['supplied_products'] as $key => $value) {
                    ?>
                    <tr>
                        <td><?= $key ?></td>
                        <td><?= $value['amount'] ?></td>
                        <td><?= $value['cost'] ?></td>
                        <td><a href="index.php?supplied=remove&product=<?= $key ?>">Remove</a></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <p>
                <input type="submit" name="save_products" value="Store product"/>
            </p>
        </form>
        <?php
    } else {
        echo "Add supplied product";
    }
    ?>

    <p>
        <b>New product</b>
    </p>
    <form action="index.php" method="post">
        <table border="1">
            <tr>
                <th>Product</th>
                <th>Amount</th>
                <th>Cost</th>
            </tr>
            <tr>
                <td>
                    <input type="text" name="supplied_product" required>
                </td>
                <td>
                    <input type="number" name="supplied_amount" min="0.01" step="0.01" required>
                </td>
                <td>
                    <input type="number" name="supplied_cost" min="0.01" step="0.01" required>
                </td>
            </tr>
        </table>
        <p>
            <input type="submit" name="add_product" value="Add product"/>
        </p>
    </form>
    <?php
} else {
    echo "There are no awaiting deliveries";
}