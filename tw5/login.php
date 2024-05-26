<?php
include('config.php');
/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
session_start();

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
    // Если есть логин в сессии, то пользователь уже авторизован.
    // TODO: Сделать выход (окончание сессии вызовом session_destroy()
    //при нажатии на кнопку Выход).
    // Делаем перенаправление на форму.
    header('Location: ./');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    ?>
    <html>
    <head>
        <link rel="icon" type="image/x-icon" href="favicon.svg">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
        <title>Логин</title>
        <link rel="stylesheet" href="style5.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js" defer></script>
    </head>
    <body>
    <div class="mx-auto col col-4 " id="forma">
        <form id="form1" action="" method="POST">
            <div class="form-group">
                <label for="name">Логин</label>
                <input name="login" id="name" class="form-control" placeholder="Введите ваш логин" ">
            </div>
            <div class="form-group">
                <label for="pwd">Пароль</label>

                <input name="pass" class="form-control" id="pwd" placeholder="Введите ваш пароль" >

            </div>

            <input type="submit" id="btnend" class="btn btn-primary" value="Отправить">
        </form>
    </div>
    </div>
    </body>


    </html>
    <?php
}

else {
    try {
        $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD, [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $stmt = $db->prepare("SELECT * FROM user 
      where user=?");
        $stmt -> execute([$_POST['login']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $flag=false;
        if(password_verify($_POST['pass'],$result["pass"]))
        {
            $_SESSION['login'] = $_POST['login'];

            $_SESSION['uid'] =$result["id"];
            header('Location: ./');
        }


    }
    catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();

    }
}