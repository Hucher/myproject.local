<?php
require_once 'User/function.php';
session_start();
$userData = [
    'user_id' => $_GET['id'],
    'status' => $_POST['status'],
    'authUserRole' => $_SESSION['infoUser']['role'],
    'authUserId' => $_SESSION['infoUser']['user_id']
];

if ($userData['authUserRole'] === 'admin' || $userData['authUserId'] === $userData['user_id']) {
    $result = updateStatus($userData['user_id'], $userData['status'], $pdo);
    $_SESSION['success'] = 'Статус успешно установлен.';
    setFlashMessage('success', $_SESSION['success']);
    redirectTo('page_profile.php?id=' . $userData['user_id']);
}
$_SESSION['warning'] = 'У вас не прав на установку статуса.';
setFlashMessage('warning', $_SESSION['warning']);
redirectTo('users.php');
