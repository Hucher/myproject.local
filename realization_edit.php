<?php
require_once 'User/function.php';
session_start();
$userData = [
    'user_id' => $_GET['id'],
    'authUser' => $_SESSION['infoUser']['user_id'],
    'name' => $_POST['name'],
    'position' => $_POST['position'],
    'phone' => $_POST['phone'],
    'address' => $_POST['address'],
    'role' => $_SESSION['infoUser']['role']
];

$result = updateInfo($userData['user_id'], $userData['name'], $userData['position'], $userData['phone'], $userData['address'], $pdo);
$_SESSION['success'] = 'Профиль успешно обновлен';
setFlashMessage('success', $_SESSION['success']);
redirectTo('page_profile.php');

