
<?php
include('module.php');
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {



    // В суперглобальном массиве $_GET PHP хранит все параметры, переданные в текущем запросе через URL.
    if (!empty($_COOKIE['save'])) {

        clearLoginCookie();

        $messages[] = 'Спасибо, результаты сохранены.';

        if (!empty($_COOKIE['pass'])) {
            $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
                strip_tags($_COOKIE['login']),
                strip_tags($_COOKIE['pass']));
        }
    }

    $errors = array();
    $errors['fio'] = !empty($_COOKIE['fio_error']);
    $errors['tel'] = !empty($_COOKIE['tel_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['date_of_birth'] = !empty($_COOKIE['date_of_birth_error']);
    $errors['gender'] = !empty($_COOKIE['gender_error']);
    $errors['languages'] = !empty($_COOKIE['languages_error']);
    $errors['bio'] = !empty($_COOKIE['bio_error']);
    $errors['checkbox'] = !empty($_COOKIE['checkbox_error']);

    if ($errors['fio']) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('fio_error', '', 100000);
        // Выводим сообщение.
        $messages[] = 'Заполните корректно имя. Минимум 5 символов, только русские и английские буквы.(Не должно быть пустым.
  Должно  содержать только русские и английские буквы, запятые, точки, пробелы и дефисы.
  Должно быть длиннее 4 символов.)<br/>';
    }
    if ($errors['tel']) {
        setcookie('tel_error', '', 100000);
        $messages[] = 'Пожалуйста, введите корректный номер телефона. Номер телефона должен содержать больше 6 символов<br/>';
    }
    if ($errors['email']) {
        setcookie('email_error', '', 100000);
        $messages[] = 'Заполните корректно email.(Не должно быть пустым.
    Должно соответствовать формату электронной почты.)<br/>';
    }
    if ($errors['date_of_birth']) {
        setcookie('date_of_birth_error', '', 100000);
        $messages[] = 'Заполните корректно дату рождения в формате дд/мм/гггг.<br/> ';
    }
    if ($errors['gender']) {
        setcookie('gender_error', '', 100000);
        $messages[] = 'Выберите пол.<br/>';
    }

    if ($errors['languages']) {
        setcookie('languages_error', '', 100000);
        $messages[] = 'Выберите хотя бы один ЯП.<br/>';
    }

    if ($errors['bio']) {
        setcookie('bio_error', '', 100000);
        $messages[] = 'Заполните корректно биографию. (Не должно быть пустым.
    Должно содержать только русские и английские буквы, цифры, запятые, точки, пробелы и дефисы.)<br/>';
    }

    if ($errors['checkbox']) {
        setcookie('checkbox_error', '', 100000);
        $messages[] = 'Вы не согласились с условиями контракта.<br/>';
    }

// Складываем предыдущие значения полей в массив, если есть.
    $values = array();
    $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
    $values['tel'] = empty($_COOKIE['tel_value']) ? '' : $_COOKIE['tel_value'];
    $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
    $values['date_of_birth'] = empty($_COOKIE['date_of_birth_value']) ? '' : $_COOKIE['date_of_birth_value'];
    $values['gender'] = empty($_COOKIE['gender_value']) ? '' : $_COOKIE['gender_value'];
    $values['languages'] = empty($_COOKIE['languages_value']) ? array() :
        unserialize($_COOKIE['languages_value']);
    $values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
    $values['checkbox'] = empty($_COOKIE['checkbox_value']) ? '' : $_COOKIE['checkbox_value'];
    if (count(array_filter($errors)) === 0 && !empty($_COOKIE[session_name()]) &&
        session_start() && !empty($_SESSION['login'])) {


        $stmt = $db->prepare("SELECT * FROM application2 where user_id=?");
        $stmt -> execute([$_SESSION['uid']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $values['fio'] = empty($result[0]['name']) ? '' : strip_tags($result[0]['name']);
        $values['tel'] = empty($result[0]['tel']) ? '' : strip_tags($result[0]['tel']);
        $values['email'] = empty($result[0]['email']) ? '' : strip_tags($result[0]['email']);
        $values['date_of_birth'] = empty($result[0]['date_of_birth']) ? '' :strip_tags($result[0]['date_of_birth']);
        $values['gender'] = empty($result[0]['gender']) ? '' : strip_tags($result[0]['gender']);


        $values['bio'] = empty($result[0]['bio']) ? '' : strip_tags($result[0]['bio']);
        $values['checkbox'] = empty($result[0]['checkbox']) ? '' : strip_tags($result[0]['checkbox']);


        $stmt = $db->prepare("SELECT * FROM app_language2 where id_app=(SELECT id FROM application2 where user_id=?) ");


        $stmt -> execute([$_SESSION['uid']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $res) {
            $values['languages'][$res["id_pl"]-1] = empty($res) ? '' : strip_tags($res["id_pl"]);
        }

        printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
    }

    include('form.php');
    // Завершаем работу скрипта.

}
else{
    if (isset($_POST['logout']) && $_POST['logout'] == 'true') {
        session_destroy();
        setcookie(session_name(), '', time() - 3600);
        setcookie('PHPSESSID', '', time() - 3600, '/');

        header('Location: ./');
        exit();
    }

    $errors = FALSE;
    if (empty($_POST['fio']) || !preg_match('/^([а-яА-ЯЁёa-zA-Z_,.\s-]+)$/u', $_POST['fio']) || strlen($_POST['fio']) < 5 ) {
        setcookie('fio_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('fio_value', $_POST['fio'], time() + 365 * 24 * 60 * 60);
    }


    if (empty($_POST['tel']) ||  strlen($_POST['tel']) <= 6) {
        setcookie('tel_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('tel_value', $_POST['tel'], time() + 365 * 24 * 60 * 60);
    }

    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL )) {
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('email_value', $_POST['email'], time() + 365 * 24 * 60 * 60);
    }

    if (empty($_POST['date_of_birth']) || !preg_match('%[1-2][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]%', $_POST['date_of_birth'])) {
        setcookie('date_of_birth_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('date_of_birth_value', $_POST['date_of_birth'], time() + 365 * 24 * 60 * 60);
    }

    if (empty($_POST['gender']) || !in_array($_POST['gender'], ['w','m'])) {
        setcookie('gender_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }else {
        if( !in_array($_POST['gender'], ['w','m'])){
            $errors = TRUE;
            setcookie('gender_error', '1', time() + 24 * 60 * 60);
        }
        setcookie('gender_value', $_POST['gender'], time() + 365 * 24 * 60 * 60);
    }


    if (empty($_POST['languages'])) {
        setcookie('languages_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        foreach ($_POST['languages'] as $language) {
            if(!in_array($language, [1,2,3,4,5,6,7,8,9,10,11])){
                setcookie('languages_error', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
                break;
            }
        }
        $abs=array();

        foreach ($_POST['languages'] as $res) {
            $abs[$res-1] = $res;
        }
        setcookie('languages_value', serialize($abs), time() + 365 * 24 * 60 * 60);
    }


    if (empty($_POST['bio']) || !preg_match('/^([а-яА-ЯЁёa-zA-Z0-9_,.\s-]+)$/u', $_POST['bio'])) {
        setcookie('bio_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('bio_value', $_POST['bio'], time() + 365 * 24 * 60 * 60);
    }


    if (empty($_POST['checkbox'])|| $_POST['checkbox']!=1) {
        setcookie('checkbox_error', '1', time() + 24 * 60 * 60);
        setcookie('checkbox_value', '0', time() + 365 * 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('checkbox_value', '1', time() + 365 * 24 * 60 * 60);
    }


    if ($errors) {
        header('Location: index.php');
        exit();
    }
    else{
        clearErrorCookie();
    }


    if (!empty($_COOKIE[session_name()]) &&
        session_start() && !empty($_SESSION['login'])) {

        $stmt = $db->prepare("UPDATE application2 SET name= ?,tel= ?,email= ?,date_of_birth= ?,gender= ?,bio= ?,checkbox= ?  WHERE user_id = ?");

        $stmt -> execute([$_POST['fio'],$_POST['tel'], $_POST['email'], $_POST['date_of_birth'], $_POST['gender'], $_POST['bio'], $_POST['checkbox'], $_SESSION['uid']]);




        $stmt = $db->prepare("SELECT * FROM app_language2 where id_app=(SELECT id FROM application2 where user_id=?) ");
        $stmt -> execute([$_SESSION['uid']]);
        $result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $c=0;
        $flag=false;
        foreach ( $_POST['languages'] as $language) {
            if(isset($result2[$language-1]['id_pl'])){
                if ($result2[$language-1]['id_pl']!=$language){
                    $flag=true;
                    break;
                }
            }
            else {
                $flag=true;
                break;
            }
        }





        if($flag){
            $stmt = $db->prepare("DELETE FROM app_language2 WHERE id_app=(SELECT id FROM application2 where user_id=?) ");
            $stmt -> execute([$_SESSION['uid']]);

            $stmt = $db->prepare("SELECT id FROM application2 where user_id=? ");
            $stmt -> execute([$_SESSION['uid']]);
            $result3 = $stmt->fetchAll(PDO::FETCH_ASSOC);


            $stmt = $db->prepare("INSERT INTO app_language2 (id_app, id_pl) VALUES (?,?)");
            foreach ($_POST['languages'] as $language) {
                $stmt->execute([$result3[0]["id"], $language]);
            }
        }





    }
    else
    {
        $login = substr(uniqid('', true), -8, 8);
        $pass = uniqid();

        setcookie('login', $login);
        setcookie('pass', $pass);

// Подготовленный запрос. Не именованные метки.
        try {
            $stmt = $db->prepare("INSERT INTO user (user, pass) VALUES (?,?)");
            $stmt -> execute([$login, password_hash($pass, PASSWORD_DEFAULT)]);
            $id = $db->lastInsertId();
            $stmt = $db->prepare("INSERT INTO application2 (name,tel,email,date_of_birth,gender,bio,checkbox, user_id) VALUES 
  (?,?,?,?,?,?,?,?)");
            $stmt -> execute([$_POST['fio'],$_POST['tel'], $_POST['email'], $_POST['date_of_birth'], $_POST['gender'], $_POST['bio'], $_POST['checkbox'], $id]);
            $id = $db->lastInsertId();
            $stmt = $db->prepare("INSERT INTO app_language2 (id_app, id_pl) VALUES (?,?)");
            foreach ($_POST['languages'] as $ability) {
                $stmt->execute([$id, $ability]);
            }




        }
        catch(PDOException $e){
            print('Error : ' . $e->getMessage());
            exit();
        }
    }

// Делаем перенаправление.
// Если запись не сохраняется, но ошибок не видно, то можно закомментировать эту строку чтобы увидеть ошибку.
    setcookie('save', '1');
    if(!empty($_SESSION['admin'])){
        header('Location: ./admin.php');
    }
    else{
        header('Location: ?save=1');
    }
}
