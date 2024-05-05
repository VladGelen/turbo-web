<?php
/**
 * Реализовать проверку заполнения обязательных полей формы в предыдущей
 * с использованием Cookies, а также заполнение формы по умолчанию ранее
 * введенными значениями.
 */

// Подключение к базе данных
global $user, $pass;
include('../tw4/config.php');

// Установка правильной кодировки
header('Content-Type: text/html; charset=UTF-8');

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Проверка наличия куков с выбранными значениями языков программирования
    $selectValues = isset($_COOKIE['select_value']) ? explode(',', $_COOKIE['select_value']) : [];

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
    $errors = [
        'fio' => !empty($_COOKIE['fio_error']),
        'tel' => !empty($_COOKIE['tel_error']),
        'email' => !empty($_COOKIE['email_error']),
        'date' => !empty($_COOKIE['date_error']),
        'gender' => !empty($_COOKIE['gender_error']),
        'select' => !empty($_COOKIE['select_error']),
        'bio' => !empty($_COOKIE['bio_error']),
        'checkbox' => !empty($_COOKIE['checkbox_error']),
    ];

    // Вывод сообщений об ошибках
    foreach (['fio', 'tel', 'email', 'date', 'gender', 'select', 'bio', 'checkbox'] as $field) {
        if ($errors[$field]) {
            // Удаление куки с ошибкой и значением
            setcookie($field.'_error', '', time() - 3600);
            setcookie($field.'_value', '', time() - 3600);
            // Добавление сообщения об ошибке
            $messages[] = '<div class="error">Заполните ' . getFieldLabel($field) . '.</div>';
        }
    }

    // Получение ранее введенных значений полей из куков
    $values = [];
    foreach (['fio', 'tel', 'email', 'date', 'gender', 'select', 'bio', 'checkbox'] as $field) {
        $values[$field] = isset($_COOKIE[$field.'_value']) ? $_COOKIE[$field.'_value'] : '';
    }

    // Вывод формы
    include('form.php');
}
// Обработка данных формы
else {
    // Подключение к базе данных
    try {
        $db = new PDO("mysql:host=localhost;dbname=u67450", $user, $pass, [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch(PDOException $e) {
        echo "Ошибка подключения: " . $e->getMessage();
        exit;
    }

    // Проверка ошибок
    $errors = [];
    foreach (['fio', 'tel', 'email', 'date', 'gender', 'bio', 'checkbox'] as $field) {
        // Проверка наличия данных и их формат
        if (empty($_POST[$field])) {
            $errors[$field] = true;
            setcookie($field.'_error', '1', time() + 24 * 60 * 60);
        } else {
            setcookie($field.'_value', htmlspecialchars($_POST[$field]), time() + 30 * 24 * 60 * 60);
        }
    }

    // Проверка выбранных языков программирования
    $selectValues = isset($_POST['select']) ? $_POST['select'] : [];
    foreach ($selectValues as $value) {
        if (!in_array($value, ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'])) {
            $errors['select'] = true;
            setcookie('select_error', '1', time() + 24 * 60 * 60);
            break;
        }
    }

    // Перенаправление при наличии ошибок
    if (!empty($errors)) {
        header('Location: index.php');
        exit();
    }

    // Вставка данных в базу данных
    try {
        // Вставка данных о пользователе
        $stmt_user = $db->prepare("INSERT INTO users (fio, tel, email, date, gender, bio, checkbox) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_user->execute([$_POST['fio'], $_POST['tel'], $_POST['email'], $_POST['date'], $_POST['gender'], $_POST['bio'], $_POST['checkbox']]);

        // Получение ID только что добавленного пользователя
        $user_id = $db->lastInsertId();

        // Вставка данных о выбранных языках программирования
        $stmt_lang = $db->prepare("INSERT INTO user_programming_languages (user_id, lang_id) VALUES (?, ?)");
        foreach ($selectValues as $lang_id) {
            $stmt_lang->execute([$user_id, $lang_id]);
        }

        // Удаление Cookies с признаками ошибок и значений
        foreach (['fio', 'tel', 'email', 'date', 'gender', 'select', 'bio', 'checkbox'] as $field) {
            setcookie($field.'_error', '', time() - 3600);
            setcookie($field.'_value', '', time() - 3600);
        }

        // Установка Cookies с признаком успешного сохранения
        setcookie('save', '1');

        // Перенаправление на страницу без параметров
        header('Location: index.php');
        exit();
    } catch (PDOException $e) {
        // Обработка ошибок базы данных
        echo 'Ошибка базы данных: ' . $e->getMessage();
        exit();
    }
}

// Функция для получения метки поля
function getFieldLabel($field) {
    $labels = [
        'fio' => 'ФИО',
        'tel' => 'телефон',
        'email' => 'эл. почта',
        'date' => 'дату рождения',
        'gender' => 'пол',
        'select' => 'языки программирования',
        'bio' => 'биографию',
        'checkbox' => 'согласие с правилами',
    ];
    return isset($labels[$field]) ? $labels[$field] : '';
}