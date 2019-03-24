<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 03.03.19
 * Time: 19:32
 */

if (isset($_POST['submit_lab1'])) {
    unset($_POST['submit_lab1']);
    $_POST['date'] = date("Y-m-d H:i:s");
    $enumName = [
        'first_name' => 'Имя',
        'last_name' => 'Фамилия',
        'middle_name' => 'Отчество',
        'address' => 'Адресс',
        'phone' => 'Телефон',
        'email' => 'Email',
        'date' => 'Дата/время выполнени запроса',
    ];
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>lab1</title>
        <div>
            <a href="lab1.php">back</a>
            <a href="index.php">labs</a>
            <hr>
        </div>
        <?php
        foreach ($_POST as $key => $value) {
            ?>
            <span><i><?= $enumName[$key] ?>:</i>
                <?php
                if ($key === 'email') {
                    ?>
                    <b><a href="mailto:<?= $value ?>"><?=$value?></a></b>
                    <?php
                } elseif ($key === 'phone') {
                    ?>
                    <b><a href="tel:+<?= $value ?>"><?=$value?></a></b>
                    <?php
                } else {
                    ?>
                    <b><?= $value ?></b>
                    <?php
                }
                ?>  
                </span>
            <br>
            <?php
        }
        ?>
    </head>
    <body>

    </body>
    </html>
    <?php
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>form</title>
    </head>
    <body>
    <h3>form</h3>
    <form action="lab1.php" method="post">
        <p>
            <b>Имя:</b>
        </p>
        <p>
            <input type="text" name="first_name" required/>
        </p>
        <p>
            <b>Фамилия:</b>
        </p>
        <p>
            <input type="text" name="last_name" required/>
        </p>
        <p>
            <b>Отчество:</b>
        </p>
        <p>
            <input type="text" name="middle_name" required/>
        </p>
        <p>
            <b>Адресс:</b>
        </p>
        <p>
            <input type="text" name="address" required/>
        </p>
        <p>
            <b>Телефон (+38-xxx-xxx-xx-xx):</b>
        </p>
        <p>
            +<input type="tel" name="phone" pattern="38-[0-9]{3}-[0-9]{3}-[0-9]{2}-[0-9]{2}" required/>
        </p>
        <p>
            <b>Email:</b>
        </p>
        <p>
            <input type="email" name="email" required/>
        </p>
        <p>
            <input type="submit" name="submit_lab1" value="Submit"/>
            <input type="reset" name="reset" value="Reset"/>
        </p>
    </form>
    </body>
    </html>
    <?php
}

?>