<?php
require_once 'User/function.php';
session_start();
$userData = [
    'user_id' => $_GET['id'],
    'authUserRole' => $_SESSION['infoUser']['role'],
    'authUserId' => $_SESSION['infoUser']['user_id']
];
$user = getUserById($userData['user_id'], $pdo);

if ($userData['authUserRole'] === 'admin' || $userData['authUserId'] === $userData['user_id']) {
    if (isset($_FILES['image'])) {
        $errors = [];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileTmp = $_FILES['image']['tmp_name'];

        if ($fileSize > 2097152) {
            $errors[] = 'Файл должен быть 2 мб';
        }
        if (empty($errors)) {
            $result = uploadImage($fileTmp, $fileName, $pdo, $user['media_id']);
        }
    }
    $_SESSION['success'] = 'Профиль успешно обнавлен.';
    setFlashMessage('success', $_SESSION['success']);
    redirectTo('page_profile.php?id=' . $userData['user_id']);
}
$_SESSION['warning'] = 'У вас нет прав на установку картинки профиля.';
setFlashMessage('warning', $_SESSION['warning']);
redirectTo('users.php');
