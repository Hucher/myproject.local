<?php
require_once 'User/function.php';
session_start();
$userData = [
    'user_id' => $_GET['id'],
    'authUser' => $_SESSION['infoUser']['user_id'],
    'role' => $_SESSION['infoUser']['role']
];
if (isNotLoggedIn() == true) {
    $_SESSION['warning'] = 'Необходимо авторизоваться';
    setFlashMessage('warning', $_SESSION['warning']);
    redirectTo('page_login.php');
}
if ($userData['role'] !== 'admin') {
    $author = isAuthor($userData['authUser'], $userData['user_id']);
    if ($author !== true) {
        $_SESSION['warning'] = 'Можно удалять только своего пользователя';
        setFlashMessage('warning', $_SESSION['warning']);
        redirectTo('users.php');
    }
}
if($userData['authUser'] == $userData['user_id'])
{
    deleteUser($pdo ,$userData['user_id']);
    session_destroy();
    redirectTo('page_register.php');
}
else{
    deleteUser($pdo ,$userData['user_id']);
    $_SESSION['success'] = 'Пользователь успешно удален.';
    setFlashMessage('success' ,$_SESSION['success']);
    redirectTo('users.php');
}

