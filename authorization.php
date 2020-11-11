<?php
session_start();
require_once 'User/function.php';
$data = [
    'email' => $_POST['email'],
    'password' => $_POST['password']
];

$user = getAuthUser($pdo, $data['email'], $data['password']);
$_SESSION['infoUser'] = $user;

if (isNotLoggedIn()) {
    redirectTo('page_login.php');
}
$_SESSION['primary'] = 'Авторизация успешна.';
setFlashMessage('primary', $_SESSION['primary']);
redirectTo('users.php');
