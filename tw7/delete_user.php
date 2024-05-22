<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['token'], $_POST['token'])) {
        die('CSRF token validation failed');
    }

    if (!isset($_POST['id'])) {
        header("Location: admin.php");
        exit();
    }

    $user_id = $_POST['id'];

    try {
        $db = new PDO('mysql:host=localhost;dbname=' . DB_NAME, DB_USER, DB_PASS, array(PDO::ATTR_PERSISTENT => true));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Удаление связанных записей
        $stmt = $db->prepare("DELETE FROM application_languages WHERE id_app = ?");
        $stmt->execute([$user_id]);

        // Удаление пользователя
        $stmt = $db->prepare("DELETE FROM application WHERE id = ?");
        $stmt->execute([$user_id]);

        header("Location: admin.php");
        exit();
    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage();
        exit();
    }
} else {
    header("Location: admin.php");
    exit();
}
?>
