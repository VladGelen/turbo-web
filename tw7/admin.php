<?php
include('module.php');


if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
    $stmt = $db->prepare("SELECT * FROM admin
          where user=?");
    $stmt -> execute([$_SERVER['PHP_AUTH_USER']]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

}



if (!$result || !password_verify($_SERVER['PHP_AUTH_PW'], $result['pass'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация</h1>');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['set_session'])) {

    clearErrorCookie();
    clearLoginCookie();
    clearValueCookie();




    session_start();
    $_SESSION['login'] = $_POST['login'];
    $_SESSION['uid'] = $_POST['uid'];
    $_SESSION['admin']="true";
    header('Location: ./');
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {

    $stmt = $db->prepare("DELETE FROM application2 WHERE id=?");
    $stmt -> execute([$_POST['id']]);

    $stmt = $db->prepare("DELETE FROM app_language2 WHERE id_app=?");
    $stmt -> execute([$_POST['id']]);


}


$stmt = $db->prepare("SELECT title, count(*) AS count FROM app_language2 a
JOIN p_languages p
ON a.id_pl=p.id 
GROUP BY
a.id_pl;");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<html>
<head>
    <link rel="icon" type="image/x-icon" href="favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <title>Админ</title>

    <style>
        html, body {
            font-family: monospace;
            font-size: 15px;
        }

        #messages {
            text-align: center;
        }
        body {
            padding: 10px;
        }

    </style>
</head>
<body>
<div class="col col-9 mx-auto my-5">
    <?php
    echo "<table class='table'>";
    echo "<tr><th>Языки программирования </th><th>Количество</th></tr>";

    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['count']) . "</td>";
        echo "</tr>";
    }

    echo "</table>"; ?>
</div>
<form action="" method="POST">
    <table class="table table-bordered">
        <tr>
            <th scope="col">id</th>
            <th scope="col">Имя</th>
            <th scope="col">Телефон</th>
            <th scope="col">Email</th>
            <th scope="col">Дата рождения</th>
            <th scope="col">Пол</th>
            <th scope="col">Языки программирования</th>
            <th scope="col">Биография</th>
            <th scope="col">UID</th>
            <th scope="col">Выбор</th>
        </tr>
        <?php
        $stmt = $db->prepare("SELECT * FROM application2 a
            JOIN user u
            ON a.user_id=u.id");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $counter = 0;

        foreach ($result as $res) {

            $stmtLang = $db->prepare("SELECT id_pl FROM app_language2 WHERE id_app=?");
            $stmtLang->execute([$res['id']]);
            $langs = $stmtLang->fetchAll(PDO::FETCH_COLUMN, 0);

            ?>
            <tr>
                <td><?= htmlspecialchars($res['id']) ?></td>
                <td><input disabled type="text" name="fio[]" class="form-control" value="<?= htmlspecialchars($res['name']) ?>"></td>
                <td><input disabled type="text" name="tel[]" class="form-control" value="<?= htmlspecialchars($res['tel']) ?>"></td>
                <td><input disabled type="email" name="email[]" class="form-control" value="<?= htmlspecialchars($res['email']) ?>"></td>
                <td><input disabled type="date" name="date_of_birth[]" class="form-control" value="<?= $res['date_of_birth'] ?>"></td>
                <td>
                    <select disabled name="gender<?= $counter ?>" class="form-control">
                        <option value="m" <?= $res['gender'] == 'm' ? 'selected' : '' ?>>Муж</option>
                        <option value="f" <?= $res['gender'] == 'w' ? 'selected' : '' ?>>Жен</option>
                    </select>
                </td>
                <td>
                    <select disabled multiple name="languages<?= $counter ?>[]" class="form-control">
                        <?php
                        $stmtP = $db->prepare("SELECT * FROM p_languages");
                        $stmtP->execute();
                        $pLangs = $stmtP->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($pLangs as $pLang) {
                            echo '<option value="' . $pLang['id'] . '"' . (in_array($pLang['id'], $langs) ? ' selected' : '') . '>' . htmlspecialchars($pLang['title']) . '</option>';
                        }
                        ?>
                    </select>
                </td>
                <td><textarea disabled name="bio[]" rows="3" class="form-control"><?= htmlspecialchars($res['bio']) ?></textarea></td>
                <td><?= htmlspecialchars($res['user_id']) ?></td>
                <td>            <form action="" method="POST">
                        <input type="hidden" name="login" value="<?= htmlspecialchars($res['user']) ?>">
                        <input type="hidden" name="uid" value="<?= htmlspecialchars($res['user_id']) ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($res['id']) ?>">
                        <button type="submit" name="set_session" class="btn btn-primary">Редактировать</button>
                        <button type="submit" name="delete" class="btn btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
            <?php
            $counter++;
        }
        ?>
    </table>
</form>
</body>
</html>