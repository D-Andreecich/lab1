<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 02.04.19
 * Time: 22:33
 */

require_once '../connect.php';
$dbName = "supply";
$conn = db_conn($dbName);

$errors = [];

session_start();

if (isset($_POST['create_user']) && isset($_SESSION['user']) && isset($_SESSION['user_data']['actions']) && in_array('create', $_SESSION['user_data']['actions'])) {

    $data = $_POST;
    unset($data['create_user']);

    $data['login'] = htmlspecialchars($data['login']);
    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
    $data['actions'] = json_encode($data['actions']);
    $data['exclude_tables'] = implode(',', array_map(function ($item) {
        return "'{$item}'";
    }, $data['exclude_tables']));

    $sql = "INSERT INTO `users` (`login`, `password`, `exclude_tables`, `actions`) VALUES ('{$data['login']}', '{$data['password']}', \"{$data['exclude_tables']}\", '{$data['actions']}')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header("location: /lab7.php?table=users");
    } else {
        var_dump($data, $result, $sql);
        die('<b>Error:</b> ' . mysqli_error($conn));
    }


} else if (isset($_SESSION['user']) && isset($_SESSION['user_data']['actions']) && in_array('create', $_SESSION['user_data']['actions'])) {
    $sql = "SHOW TABLES";
    $result = mysqli_query($conn, $sql);
    $tablesArr = array_column(mysqli_fetch_all($result, MYSQLI_ASSOC), 'Tables_in_supply');
} else {
    session_unset();
    header("location: /lab7.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <style>
        .flex-container {
            display: flex;
        }
        .items {
           padding: 10px;
        }
    </style>
</head>
<body>
<div>
    <a href="/lab7.php">back</a>
    <a href="/index.php">labs</a>
    <hr>
</div>
<h3>Create User</h3>
<?php
foreach ($errors as $key => $value) {
    echo "<div style='color: red'> $value </div><br/>";
}
?>
<div>
    <form action="/lab7/createUser.php" method="post" class="flex-container">
        <div class="items">
            <p>
                <b>Login:</b>
            </p>
            <p>
                <input type="text" name="login" required/>
            </p>
        </div>
        <div class="items">
            <p>
                <b>Password:</b>
            </p>
            <p>
                <input type="password" name="password" required/>
            </p>
        </div>
        <div class="items">
            <p>
                <b>Exclude tables:</b>
            </p>
            <p>
                <select name="exclude_tables[]" multiple>
                    <?php
                    foreach ($tablesArr as $nameTable) {
                        echo "<option value='{$nameTable}'>{$nameTable}</option>";
                    }
                    ?>
                </select>
            </p>
        </div>
        <div class="items">
            <p>
                <b>Actions:</b>
            </p>
            <p>
                <select name="actions[]" multiple>
                    <option value="insert">insert</option>
                    <option value="update">update</option>
                    <option value="delete">delete</option>
                    <option value="create">create</option>
                </select>
            </p>
        </div>
        <div class="items">
            <p>
                <input type="submit" name="create_user" value="Create"/>
            </p>
        </div>
    </form>
</div>
</body>
</html>