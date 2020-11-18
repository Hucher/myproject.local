<?php
require_once 'User/function.php';
session_start();
$userData = [
    'email' => $_POST['email'],
    'password' => $_POST['password'],
    'name' => $_POST['name'],
    'position' => $_POST['position'],
    'phone' => $_POST['phone'],
    'address' => $_POST['address'],
    'status' => $_POST['status'],
    'vk' => $_POST['vk'],
    'telegram' => $_POST['telegram'],
    'instagram' => $_POST['instagram'],
    'image' => $_FILES['image']
];

$isEmailUser = getEmailUser($pdo, $userData['email']);
if (!empty($isEmailUser)) {
    $_SESSION['warning'] = 'Такой email уже существует';
    setFlashMessage('warning', 'Такой email занят другим пользователем');
    redirectTo('create_user.php');
}

$userId = add_User($userData['email'], $userData['password'], $pdo);
editInformation($userId, $userData['name'], $userData['position'], $userData['phone'], $userData['address'], $pdo);
$mediaId = setStatus($userData['status'], $userId, $pdo);

if (isset($_FILES['image'])) {
    $errors = [];
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    $fileTmp = $_FILES['image']['tmp_name'];

    if ($fileSize > 2097152) {
        $errors[] = 'Файл должен быть 2 мб';
    }
    if (empty($errors)) {
        $result = uploadImage($fileTmp, $fileName, $pdo, $mediaId);
    }
}
addSocialLinks($mediaId, $userData['telegram'], $userData['instagram'], $userData['vk'], $pdo);
$_SESSION['success'] = 'Профиль успешно создан.';
setFlashMessage('success' ,$_SESSION['success']);
redirectTo('users.php');



