<?php
/**
 * Файл index.php для авторизованного пользователя отображает форму,
 * содержащую ранее введенные данные.
 */

// Проверяем, авторизован ли пользователь, если нет, перенаправляем на страницу логина.
session_start();
if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

// Для удобства обращения к полям формы будем использовать массив с их именами.
$form_fields = ['fio', 'tel', 'email', 'date', 'gender', 'select', 'bio', 'checkbox'];

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Массив для временного хранения сообщений пользователю
    $messages = [];

    // Проверка наличия куки с признаком успешного сохранения
    if (!empty($_COOKIE['save'])) {
        // Удаление куки
        setcookie('save', '', time() - 3600);
        // Добавление сообщения о сохранении
        $messages[] = 'Спасибо, результаты сохранены.';
    }

    // Проверка наличия ошибок
    $errors = [];
    foreach ($form_fields as $field) {
        $errors[$field] = !empty($_COOKIE[$field.'_error']);
    }

    // Получение ранее введенных значений полей из куков
    $values = [];
    foreach ($form_fields as $field) {
        $values[$field] = isset($_COOKIE[$field.'_value']) ? $_COOKIE[$field.'_value'] : '';
    }

    // Вывод формы
    include('form.php');
}
?>
