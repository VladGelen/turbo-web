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
// Валидация имени

if (empty($_POST['fio'])) {
    $errors = TRUE;
    echo 'Пожалуйста, введите ваше имя.<br>';
}



// Валидация телефона

if (empty($_POST['tel'])) {
    $errors = TRUE;
    echo 'Пожалуйста, введите ваш номер телефона.<br>';
}



// Валидация email

if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors = TRUE;
    echo 'Пожалуйста, введите корректный email.<br>';
}



// Валидация даты

if (empty($_POST['date']) || !strtotime($_POST['date'])) {
    $errors = TRUE;
    echo 'Пожалуйста, введите корректную дату.<br>';
}



    if ($errors) {
        exit();
    }

    try {
        $user = 'u67450';
        $pass = '4290181';
        $db = new PDO('mysql:host=localhost;dbname=u67450', $user, $pass,
            [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

   
        $stmt_user = $db->prepare("INSERT INTO users (fio, tel, email, date, gender, bio, checkbox) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_user->execute([$_POST['fio'], $_POST['tel'], $_POST['email'], $_POST['date'], $_POST['gender'], $_POST['bio'], $_POST['checkbox']]);


        $user_id = $db->lastInsertId();


        $selected_languages = $_POST['select'];


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