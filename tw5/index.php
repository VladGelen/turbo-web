<?php
// index.php

session_start();

// Если пользователь не авторизован, перенаправляем его на страницу входа
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Перенаправляем на форму редактирования данных
header('Location: form.php');
exit();
?>
