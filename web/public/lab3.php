<?php
/**
 * Created by IntelliJ IDEA.
 * User: d-andreevich
 * Date: 03.03.19
 * Time: 19:32
 */

$warningMessage = "";

function getUsers(): array
{
    $users = [];
    foreach (file('data') as $userStr) {
        $userArr = explode(' ', $userStr);
        $users[] = [
            'name' => trim(array_shift($userArr)),
            'password' => trim(array_shift($userArr)),
            'role' => trim(array_pop($userArr)),
            'l_f_m_name' => implode(' ', $userArr),
        ];
    }
    return $users;
}

function saveFeedback()
{
    unset($_POST['submit_feedback_lab3']);
    $data = $_POST;
    $data['date'] = date("Y-m-d H:i:s");


    return file_put_contents('feedback', json_encode($data) . PHP_EOL, FILE_APPEND);
}

function checkUser(&$warningMessage)
{
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['name'] === $_POST['name'] && $user['password'] === $_POST['password']) {
            return $user;
        }
    }
    $warningMessage = "Нет совпадений с введеными данными!";
    return false;
}

function getFeedback(): array
{
    $result = [];
    foreach (file('feedback') as $feedbackJSON) {
        $result[] = json_decode($feedbackJSON, true);
    }

    usort($result, function ($item1, $item2) {
        return $item1['l_f_m_name'] <=> $item2['l_f_m_name'];
    });

    return $result;
}

if (isset($_POST['submit_lab3']) && $user = checkUser($warningMessage)) {
    unset($_POST['submit_lab3']);

    ?>
    <div>
        <a href="lab3.php">back</a>
        <a href="index.php">labs</a>
        <hr>
    </div>
    <?php

    if ($user['role'] === 'user') {
        ?>
        <h3>ПРИВЕТСТВУЕМ ВАС, УВАЖАЕМЫЙ <?= $user['l_f_m_name'] ?></h3>
        <form action="lab3.php" method="post">
            <input type="hidden" name="l_f_m_name" value="<?= $user['l_f_m_name'] ?>">
            <p>
                <b>Отзыв:</b>
            </p>
            <p>
                <textarea name="feedback" required></textarea>
            </p>
            <p>
                <b>Email:</b>
            </p>
            <p>
                <input type="email" name="email" required/>
            </p>
            <div>
                <input type="reset" name="reset_feedback_lab3" value="reset"/>
                <input type="submit" name="submit_feedback_lab3" value="Далее"/>
            </div>
        </form>
        <?php
    } elseif ($user['role'] === 'admin') {
        foreach (getFeedback() as $feedback) {
            ?>
            <p>
                <b>ФИО:</b>
            </p>
            <p>
                <span><?= $feedback['l_f_m_name'] ?></span>
            </p> <p>
                <b>Отзыв:</b>
            </p>
            <p>
                <span><?= $feedback['feedback'] ?></span>
            </p>
            <p>
                <b>Email:</b>
            </p>
            <p>
                <span><?= $feedback['email'] ?></span>
            </p>
            <p>
                <b>Date:</b>
            </p>
            <p>
                <span><?= $feedback['date'] ?></span>
            </p>
            <hr>
            <?php
        }
    }
} elseif (isset($_POST['submit_feedback_lab3'])) {
    saveFeedback();
    ?>
    <div>
        <a href="lab3.php">back</a>
        <a href="index.php">labs</a>
        <hr>
    </div>
    <div>
        <span>Спасибо, Ваш отзыв сохранен!</span>
    </div>
    <?php
} else {
    ?>
    <html>
    <head>
        <meta charset="utf-8">
        <title>form</title>
    </head>
    <body>
    <div>
        <a href="index.php">labs</a>
        <hr>
    </div>
    <h3>form</h3>
    <form action="lab3.php" method="post">
        <div>
            <span><b>Имя</b></span> <input type="text" name="name" required/>
        </div>
        <br>
        <div>
            <span><b>Пароль</b></span> <input type="password" name="password" required/>
        </div>
        <div>
            <span style="color: red"><?= $warningMessage ?></span>
        </div>
        <div>
            <input type="reset" name="reset_lab3" value="reset"/>
            <input type="submit" name="submit_lab3" value="Далее"/>
        </div>
    </form>
    </body>
    </html>
    <?php
}