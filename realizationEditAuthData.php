<?php
require_once 'User/function.php';
session_start();
$userData = [
    'user_id' => $_GET['id'],
    'email' => $_POST['email'],
    'password' => $_POST['password'],
    'authUser' => $_SESSION['infoUser']['user_id'],
];

$user = getEmailUser($pdo, $userData['email']);

if ((isset($user['email']) && $userData['authUser'] === $userData['user_id']) || !isset($user['email'])) {
    $result = updateAuthUser($userData['user_id'], $userData['email'], $userData['password'], $pdo);
    $_SESSION['success'] = 'Авторизационные данные пользователя успешно обновлены';
    setFlashMessage('success', $_SESSION['success']);
    redirectTo('page_profile.php?id=' . $userData['user_id']);

}
$_SESSION['warning'] = 'Такой email уже используется другим пользователем';
setFlashMessage('warning', $_SESSION['warning']);
redirectTo('security.php?id=' . $userData['user_id']);


