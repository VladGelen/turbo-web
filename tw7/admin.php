<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
<?php
session_start();
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];

require 'config.php';

if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' ||
    md5($_SERVER['PHP_AUTH_PW']) != md5('123')) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация</h1>');
    exit();
}

try {
    $db = new PDO('mysql:host=localhost;dbname=' . DB_NAME, DB_USER, DB_PASS, array(PDO::ATTR_PERSISTENT => true));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}

$stmt = $db->query("SELECT * FROM application");
$usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<table border="1">';
echo '<tr><th>Имя</th><th>Телефон</th><th>Email</th><th>Год рождения</th><th>Пол</th><th>Биография</th><th>Языки программирования</th><th>Действия</th></tr>';
foreach ($usersData as $userData) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($userData['names'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td>' . htmlspecialchars($userData['phones'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td>' . htmlspecialchars($userData['email'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td>' . htmlspecialchars($userData['dates'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td>' . htmlspecialchars($userData['gender'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td>' . htmlspecialchars($userData['biography'], ENT_QUOTES, 'UTF-8') . '</td>';

    $stmt = $db->prepare("SELECT id_lang FROM application_languages WHERE id_app = ?");
    $stmt->execute([$userData['id']]);
    $userLanguages = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo '<td>' . htmlspecialchars(implode(', ', $userLanguages), ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td><a href="edit_user.php?id=' . htmlspecialchars($userData['id'], ENT_QUOTES, 'UTF-8') . '">Редактировать</a> | 
          <form action="delete_user.php" method="post" style="display:inline;">
            <input type="hidden" name="id" value="' . htmlspecialchars($userData['id'], ENT_QUOTES, 'UTF-8') . '">
            <input type="hidden" name="token" value="' . $token . '">
            <input type="submit" value="Удалить">
          </form></td>';
    echo '</tr>';
}
echo '</table>';

$stmt = $db->query("SELECT id_lang, COUNT(*) AS count FROM application_languages GROUP BY id_lang");
$languagesStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<h2>Статистика по языкам программирования</h2>';
echo '<ul>';
foreach ($languagesStats as $languageStat) {
    echo '<li>' . htmlspecialchars($languageStat['id_lang'], ENT_QUOTES, 'UTF-8') . ': ' . htmlspecialchars($languageStat['count'], ENT_QUOTES, 'UTF-8') . ' пользователей</li>';
}
echo '</ul>';
?>
</body>
</html>
