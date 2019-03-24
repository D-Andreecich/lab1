<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 03.03.19
 * Time: 19:32
 */

$errors = [];

function randomNumber(int $length): string
{
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= random_int(0, 9);
    }
    return $code;
}

function validateForm(array $date, &$errors): array
{
    $errors = [];
    foreach ($date as $key => $val) {
        if (!$val) {
            $errors[$key] = "<span style='color: red'>Это поле обязательно для заполнения!</span>";
        }
    }
    return $errors;
}

function checkReplace(string $name, &$errors): bool
{
    $result = $_POST[$name] === $_POST["replace_$name"];
    if (!$result) {
        $errors["replace_$name"] = "<span style='color: red'>Эти поля должны совпадать!</span>";
    }
    return $result;
}

function saveDataCookie(string $key, string $value)
{
    setcookie($key, $value, time() + 120);/* период действия - 2 мин */
    header('location: lab2.php');
}

if (isset($_POST['submit_lab2']) && !validateForm($_POST, $errors) && checkReplace('password', $errors) && checkReplace('code', $errors)) {
    unset($_POST['submit_lab2']);

    saveDataCookie("first_name", "ДОБРЫЙ ДЕНЬ, {$_POST['first_name']}");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>form</title>
</head>
<body>
<h2><?= $_COOKIE['first_name'] ?></h2>
<div>
    <a href="index.php">labs</a>
    <hr>
</div>
<h3>Ввод обязательной информации</h3>
<form action="lab2.php" method="post">
    <div>
        <span><b>Логин</b></span> <input type="text" name="login" value="<?= $_POST['login'] ?>" required/>
        <span>
                <?= $errors['login'] ?>
            </span>
    </div>
    <hr>
    <div>
        <span><b>Пароль</b></span> <input type="password" name="password" required/>
        <span>
                <?= $errors['password'] ?>
            </span>
    </div>
    <br>
    <div>
        <span><b>Повторите пароль</b></span> <input type="password" name="replace_password" required/>
        <span>
                <?= $errors['replace_password'] ?>
            </span>
    </div>
    <hr>
    <div>
        <span><b>Имя</b></span> <input type="text" name="first_name" value="<?= $_POST['first_name'] ?>" required/>
        <span>
                <?= $errors['first_name'] ?>
            </span>
    </div>
    <hr>
    <div>
        <span><b>День рождения</b></span> <input type="date" name="birthday" value="<?= $_POST['birthday'] ?>"
                                                 required/>
        <span>
                <?= $errors['birthday'] ?>
            </span>
    </div>
    <hr>
    <div>
        <span><b>Код</b></span>
        <input type="hidden" name="code" value="<?= $code = randomNumber(6) ?>"/>
        <span><?= $code ?></span>
        <span><b>Введите код</b></span> <input type="number" name="replace_code" required>
        <span>
                 <?= $errors['replace_code'] ?>
            </span>
    </div>
    <hr>
    <div>
        <a href="lab2.php">Назад</a>
        <input type="submit" name="submit_lab2" value="Далее"/>
    </div>
</form>


<!-- Ну этот код очень полезный - Защита от перетаскивания и выделения текста. -->
<script language=javaScript>document.onselectstart = new Function("return false");
    document.ondragstart = new Function("return false");
</script>
<!-- Следующий скрипт - ЗАЩИТА ОТ КОПИРОВАНИИ ИНФОРМАЦИИ -->
<script language=JavaScript>
    function notcopy() {
        alert("Извините, но с этой страницы нельзя ничего копировать!")
        return false
    }


    /* НУ И ПОСЛЕДНИЙ СКРИПТ - ЗАЩИТА ОТ ПЕЧАТИ */

    function atlpdp1() {
        for (wi = 0; wi < document.all.length; wi++) {
            if (document.all[wi].style.visibility != 'hidden') {
                document.all[wi].style.visibility = 'hidden';
                document.all[wi].id = 'atlpdpst'
            }
        }
    }

    function atlpdp2() {
        for (wi = 0; wi < document.all.length; wi++) {
            if (document.all[wi].id == 'atlpdpst')
                document.all[wi].style.visibility = ''
        }
    }

    window.onbeforeprint = atlpdp1;
    window.onafterprint = atlpdp2;


    /* Выключение Правой кнопки мыши */


    var message = "";

    function clickIE() {
        if (document.all) {
            (message);
            return false;
        }
    }

    function clickNS(e) {
        if
        (document.layers || (document.getElementById && !document.all)) {
            if (e.which == 2) {
                (message);
                return false;
            }
        }
    }

    if (document.layers) {
        document.captureEvents(Event.MOUSEDOWN);
        document.onmousedown = clickNS;
    } else {
        document.onmouseup = clickNS;
        document.oncontextmenu = clickIE;
    }
    document.oncontextmenu = new Function("return false")
</script>
</body>
</html>
    