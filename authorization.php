<?php
session_start();
require_once 'userRegistration/function.php';
$data = [
	'email' => $_POST['email'],
	'password' => $_POST['password']
];

$user = getAuthUser($pdo , $data['email'] , $data['password']);

if(!empty($user))
{
	$_SESSION['primary'] = 'Авторизация успешна.';
	setFlashMessage('primary' ,$_SESSION['primary']);
	redirectTo('page_login.php');
}
else{
	$_SESSION['warning'] = 'Не верные Email или Password.';
	setFlashMessage('warning' ,$_SESSION['warning']);
	redirectTo('page_login.php');
}

