<?php

$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD, [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$languages = [];

try {
    $stmt = $db->prepare("SELECT * FROM p_languages;");
    $stmt->execute();
    $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    print($e->getMessage());
    exit();
}
?>

<html lang="ru">

<head>
    <link rel="icon" type="image/x-icon" href="favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <title>Задание 5</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<?php

if (!empty($messages)) {
    print('<div id="messages">');
    foreach ($messages as $message) {
        print($message);
    }


    print('</div>');
}


?>
<?php
if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])){
    echo '
        <div class = "login">
        <form action="" method="POST" >
            <input type="hidden" name="logout" value="true">
            <button type="submit">Выйти</button>
        </form>
        </div>
    ';
}
else
    echo'
    <div class = "login">
    <form action="login.php" target="_blank">
    <button>Войти</button>
    </form>
    </div>
';
?>
<div id="forma" class="col-7">
    <form id="form1" action="" method="POST">
        <div class="form-group row">
            <label for="name" class="col-sm-2 col-form-label">Имя:</label>
            <div class="col-sm-10">
                <input name="fio" id="name" class="form-control <?php if ($errors['fio']) {print 'is-invalid';} ?>" placeholder="Введите ваше имя"  value="<?php print $values['fio']; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="tel" class="col-sm-2 col-form-label">Телефон:</label>
            <div class="col-sm-10">
                <input type="tel" name="tel" id="tel" class="form-control <?php if ($errors['tel']) {print 'is-invalid';} ?>"  placeholder="Введите телефон" value="<?php print $values['tel']; ?>">
            </div>
        </div>

        <div class="form-group row">
            <label for="email" class="col-sm-2 col-form-label">E-mail:</label>
            <div class="col-sm-10">
                <input name="email" type="email" class="form-control <?php if ($errors['email']) {print 'is-invalid';} ?>" id="email" placeholder="Введите вашу почту" value="<?php print $values['email']; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Дата рождения:</label>
            <div class="col-sm-10">
                <input name="date_of_birth" type="date" class="form-control <?php if ($errors['date_of_birth']) {print 'is-invalid';} ?>" value="<?php print $values['date_of_birth']; ?>" />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Пол:</label>
            <div class="col-sm-10">
                <label for="g1"><input type="radio" class="form-check-input <?php if ($errors['gender']) {print 'is-invalid';} ?>" name="gender" id="g1" value="m" <?php if ($values['gender']=='m') {print 'checked';} ?>>
                    Мужской</label>
                <label for="g2"><input type="radio" class="form-check-input <?php if ($errors['gender']) {print 'is-invalid';} ?>" name="gender" id="g2" value="w" <?php if ($values['gender']=='w') {print 'checked';} ?>>
                    Женский</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="mltplslct" class="col-sm-2 col-form-label">Языки программирования:</label>
            <div class="col-sm-10">
                <select class="form-control <?php if ($errors['languages']) {print 'is-invalid';} ?>" name="languages[]" id="mltplslct" multiple="multiple">
                    <?php foreach ($languages as $language): ?>
                        <option value="<?= htmlspecialchars($language['id']); ?>"
                            <?php if (!empty($values['languages']) && in_array($language['id'], $values['languages'])) {echo 'selected';} ?>>
                            <?= htmlspecialchars($language['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="bio" class="col-sm-2 col-form-label">Биография:</label>
            <div class="col-sm-10">
                <textarea name="bio" id="bio" rows="5" class="form-control <?php if ($errors['bio']) {print 'is-invalid';} ?>" ><?php print $values['bio']; ?></textarea>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2">Ознакомление:</div>
            <div class="col-sm-10">
                <input type="checkbox" class="form-check-input <?php if ($errors['checkbox']) {print 'is-invalid';} ?>" id="checkbox" value="1" name="checkbox" <?php if ($values['checkbox']=='1') {print 'checked';} ?>>
                с контрактом ознакомлен (а)
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-10">
                <input type="submit" class="btn btn-primary" value="Отправить">
            </div>
        </div>
    </form>
</div>
</body>