<?php
session_start();
require 'config.php';

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];

try {
    $db = new PDO('mysql:host=localhost;dbname=' . DB_NAME, DB_USER, DB_PASS, array(PDO::ATTR_PERSISTENT => true));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit();
}

if (!isset($_GET['id'])) {
    echo "Ошибка: ID пользователя не указан.";
    exit();
}

$id = intval($_GET['id']);
$stmt = $db->prepare("SELECT * FROM application WHERE id = ?");
$stmt->execute([$id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    echo "Пользователь с указанным ID не найден.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    if (!hash_equals($_SESSION['token'], $_POST['token'])) {
        die('CSRF token validation failed');
    }

    $stmt = $db->prepare("UPDATE application SET names = ?, phones = ?, email = ?, dates = ?, gender = ?, biography = ? WHERE id = ?");
    $stmt->execute([
        htmlspecialchars($_POST['names'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($_POST['phones'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($_POST['dates'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($_POST['biography'], ENT_QUOTES, 'UTF-8'),
        $id
    ]);

    header("Location: admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    if (!hash_equals($_SESSION['token'], $_POST['token'])) {
        die('CSRF token validation failed');
    }

    $stmt = $db->prepare("DELETE FROM application_languages WHERE id_app = ?");
    $stmt->execute([$id]);

    $stmt = $db->prepare("DELETE FROM application WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование пользователя</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Редактирование пользователя</h1>
    <form method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
        <label for="names">Имя:</label><br>
        <input type="text" id="names" name="names" value="<?php echo htmlspecialchars($userData['names'], ENT_QUOTES, 'UTF-8'); ?>"><br>
        <label for="phones">Телефон:</label><br>
        <input type="tel" id="phones" name="phones" value="<?php echo htmlspecialchars($userData['phones'], ENT_QUOTES, 'UTF-8'); ?>"><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email'], ENT_QUOTES, 'UTF-8'); ?>"><br>
        <label for="dates">Дата рождения:</label><br>
        <input type="date" id="dates" name="dates" value="<?php echo htmlspecialchars($userData['dates'], ENT_QUOTES, 'UTF-8'); ?>"><br>
        <label for="gender">Пол:</label><br>
        <select id="gender" name="gender">
            <option value="M" <?php if ($userData['gender'] == 'M') echo 'selected'; ?>>Мужской</option>
            <option value="F" <?php if ($userData['gender'] == 'F') echo 'selected'; ?>>Женский</option>
        </select><br>
        <label for="biography">Биография:</label><br>
        <textarea id="biography" name="biography"><?php echo htmlspecialchars($userData['biography'], ENT_QUOTES, 'UTF-8'); ?></textarea><br>
        <input type="submit" name="update" value="Сохранить изменения">
        <input type="submit" name="delete" value="Удалить пользователя" onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?')">
    </form>
</body>
</html>
