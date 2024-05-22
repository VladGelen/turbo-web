<?php
/**
 * Файл login.php реализует аутентификацию пользователя по логину и паролю.
 * После успешной аутентификации создается сессия, и пользователь перенаправляется на страницу index.php.
 */

// Подключение к базе данных
global $user, $pass;
include('config.php');

// Установка правильной кодировки
header('Content-Type: text/html; charset=UTF-8');

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Подключение к базе данных
        $db = new PDO("mysql:host=localhost;dbname=u67450", $user, $pass, [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // Проверка наличия пользователя в базе данных
        $stmt = $db->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$_POST['login']]);
        $user = $stmt->fetch();

        if ($user && password_verify($_POST['password'], $user['password_hash'])) {
            // Если пользователь существует и пароль совпадает, создаем сессию и перенаправляем на index.php
            session_start();
            $_SESSION['login'] = $_POST['login'];
            header('Location: index.php');
            exit();
        } else {
            echo '<div class="error">Неверный логин или пароль</div>';
        }
    } catch (PDOException $e) {
        echo "Ошибка подключения: " . $e->getMessage();
        exit();
    }
}
?>
