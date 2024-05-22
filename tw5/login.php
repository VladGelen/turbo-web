<?php
// login.php

// Установка правильной кодировки
header('Content-Type: text/html; charset=UTF-8');

// Стартуем сессию
session_start();

// Если пользователь уже авторизован, перенаправляем его на главную страницу
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Подключаем конфиг для подключения к базе данных
    include('config.php');

    // Подключаемся к базе данных
    try {
        $db = new PDO("mysql:host=localhost;dbname=u67450", $user, $pass, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Проверяем логин и пароль
        $stmt = $db->prepare("SELECT id, password_hash FROM users WHERE login = ?");
        $stmt->execute([$_POST['login']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($_POST['pass'], $user['password_hash'])) {
            // Устанавливаем переменные сессии
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $_POST['login'];
            // Перенаправляем на главную страницу
            header('Location: index.php');
            exit();
        } else {
            $error = 'Неверный логин или пароль';
        }
    } catch (PDOException $e) {
        $error = 'Ошибка базы данных: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
</head>
<body>
    <h1>Вход</h1>

    <?php
    if (isset($error)) {
        echo '<div class="alert alert-danger">' . $error . '</div>';
    }
    ?>

    <form action="" method="post">
        <label for="login">Логин:</label>
        <input type="text" name="login" id="login" required />
        <br />
        <label for="pass">Пароль:</label>
        <input type="password" name="pass" id="pass" required />
        <br />
        <input type="submit" value="Войти" />
    </form>
</body>
</html>
