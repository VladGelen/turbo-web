<?php
    header('Content-Type: text/html; charset=UTF-8');

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (!empty($_GET['save'])) {
            print('Спасибо, результаты сохранены.');
        }
        include('form.html');
        exit();
    }

    $errors = FALSE;

    // Проверка наличия ошибок ввода данных


    if ($errors) {
        exit();
    }

    try {
        $user = 'u67449';
        $pass = '4242897';
        $db = new PDO('mysql:host=localhost;dbname=u67449', $user, $pass,
            [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // Добавление записи о пользователе
        $stmt_user = $db->prepare("INSERT INTO users (fio, tel, email, date, gender, bio, checkbox) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_user->execute([$_POST['fio'], $_POST['tel'], $_POST['email'], $_POST['date'], $_POST['gender'], $_POST['bio'], $_POST['checkbox']]);

// Получение ID только что добавленного пользователя
        $user_id = $db->lastInsertId();

// Получение списка выбранных языков программирования
        $selected_languages = $_POST['select'];

// Добавление записей о выбранных языках программирования
        foreach ($selected_languages as $lang_id) {
            $stmt_lang = $db->prepare("INSERT INTO user_programming_languages (user_id, lang_id) VALUES (?, ?)");
            $stmt_lang->execute([$user_id, $lang_id]);
        }

    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }

    header('Location: ?save=1');
    ?>