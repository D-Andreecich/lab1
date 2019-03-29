<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 03.03.19
 * Time: 19:32
 */

?>
    <div>
        <a href="lab5.php">back</a>
        <a href="index.php">labs</a>
        <hr>
    </div>
<?php
if (isset($_POST['file_name']) && isset($_GET['action']) && $_GET['action'] === "show") {
    if (file_exists($_POST['file_name'])) {
        print "<pre>" . strip_tags(file_get_contents($_POST['file_name'])) . "</pre>";
    } else {
        echo "Не найден файл {$_POST['file_name']}";
    }
} elseif (isset($_POST['file_name']) && isset($_GET['action']) && $_GET['action'] === "save") {
    $filename = "lab5/art1.html";
    if (file_exists($filename)) {
        if (file_put_contents($_POST['file_name'], str_replace(['а'], ['о'], strip_tags(file_get_contents($filename))))) {
            echo "Файл успешно создан!" . PHP_EOL;
            $path = '/' . $_POST['file_name'];
            echo "<a href='{$path}'>Скачать</a>";
        }
    } else {
        echo "Не найден файл примера: {$filename}";
    }
} else {
    ?>
    <div>
        <span>Выполните действие с файлом</span>
    </div>
    <hr>
    <div>
        <form action="lab5.php?action=show" method="post">
            <label for="file_name">Введите путь к файлу *.html (включая имя)</label>
            <input type="text" id="file_name" name="file_name" required/>
            <button type="submit">Отобразить содержимое страницы</button>
        </form>
    </div>
    <hr>
    <div>
        <form action="lab5.php?action=save" method="post">
            <label for="file_name">Введите путь к текстовому файлу для его создания</label>
            <input type="text" id="file_name" name="file_name" required/>
            <button type="submit">Сохранить содержимое страницы в файл</button>
        </form>
    </div>
    <?php
}
