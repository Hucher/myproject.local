<?php
session_start();
require_once 'User/function.php';

$email = $_POST['email'];
$password = $_POST['password'];

//Получить email пользователя,если существует
$isUserEmail = getEmailUser($pdo, $email);

if (empty($isUserEmail['email'])) {
    addUser($email, $password, $pdo);
    $_SESSION['success'] = 'Регистрация прошла успешна.';
    setFlashMessage('success', $_SESSION['success']);
    if (isset($_SESSION['success'])) {
        redirectTo('page_login.php');
        exit();
    }
} else {
    $_SESSION['danger'] = 'Этот Email адрес занят другим пользователем.';
    setFlashMessage('danger', $_SESSION['danger']);
    redirectTo('page_register.php');
    exit();
}
 



