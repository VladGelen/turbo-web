<?php


if (!isset($_POST['id'])) {

    header("Location: admin.php");
    exit();
}

$user_id = $_POST['id'];
try {
    $db = new PDO('mysql:host=localhost;dbname=u67450', 'u67450', '4290181', array(PDO::ATTR_PERSISTENT => true));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
    exit();
}

try {
    $stmt = $db->prepare("DELETE FROM application_languages WHERE id_app = ?");
    $stmt->execute([$user_id]);
} catch (PDOException $e) {
    echo "Ошибка удаления связанных записей: " . $e->getMessage();
    exit();
}

try {
    $stmt = $db->prepare("DELETE FROM application WHERE id = ?");
    $stmt->execute([$user_id]);
    
    header("Location: admin.php");
    exit();
} catch (PDOException $e) {
    echo "Ошибка удаления пользователя: " . $e->getMessage();
    exit();
}

$allowed_pages = ['edit_user.php', 'delete_user.php'];
$page = basename($_GET['page']);
if (in_array($page, $allowed_pages)) {
    include $page;
} else {
    die('Недопустимая страница');
}

// config.php (не доступен через веб)
define('DB_USER', 'u67450');
define('DB_PASS', '4290181');
define('DB_NAME', 'u67450');

// Основной файл
require 'config.php';

$db = new PDO('mysql:host=localhost;dbname=' . DB_NAME, DB_USER, DB_PASS, array(PDO::ATTR_PERSISTENT => true));


?>