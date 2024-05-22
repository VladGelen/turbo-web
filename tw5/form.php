<?php
// form.php

session_start();

// Если пользователь не авторизован, перенаправляем его на страницу входа
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Подключаем конфиг для подключения к базе данных
include('config.php');

try {
    // Подключаемся к базе данных
    $db = new PDO("mysql:host=localhost;dbname=u67450", $user, $pass, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Получаем данные пользователя
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Обновляем данные пользователя
        $new_data = $_POST['data'];
        $stmt = $db->prepare("UPDATE users SET data = ? WHERE id = ?");
        $stmt->execute([$new_data, $_SESSION['user_id']]);
        $message = 'Данные обновлены успешно';
    }
} catch (PDOException $e) {
    $error = 'Ошибка базы данных: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма редактирования данных</title>
</head>
<body>
    <h1>Форма редактирования данных</h1>

    <?php
    if (isset($message)) {
        echo '<div class="alert alert-success">' . $message . '</div>';
    }

    if (isset($error)) {
        echo '<div class="alert alert-danger">' . $error . '</div>';
    }
    ?>

    <form action="" method="post">
        <label for="data">Данные:</label>
        <input type="text" name="data" id="data" value="<?php echo htmlspecialchars($user['data']); ?>" required />
        <br />
        <input type="submit" value="Сохранить" />
    </form>

    <a href="logout.php">Выход</a>
</body>
</html>
