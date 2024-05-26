<?php
include('config.php');
global $db;
$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD, [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);





function clearErrorCookie() {

    setcookie('fio_error', '', 100000);
    setcookie('tel_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('date_of_birth_error', '', 100000);
    setcookie('gender_error', '', 100000);
    setcookie('languages_error', '', 100000);
    setcookie('bio_error', '', 100000);
    setcookie('checkbox_error', '', 100000);
}


function clearLoginCookie() {


    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
}
function clearValueCookie() {

    setcookie('fio_value', '', 100000);
    setcookie('tel_value', '', 100000);
    setcookie('email_value', '', 100000);
    setcookie('date_of_birth_value', '', 100000);
    setcookie('gender_value', '', 100000);
    setcookie('languages_value', '', 100000);
    setcookie('bio_value', '', 100000);
    setcookie('checkbox_value', '', 100000);

}