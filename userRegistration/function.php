<?php
require_once '../dbConnect/connection.php';

//Получить пользователя по Еmail
function getEmailUser($pdo , $email)
{
	$sql = "SELECT email FROM users WHERE :email=email";

	$statement = $pdo->prepare($sql);
	$statement->execute(['email' => $email]);

	$user = $statement->fetch(PDO::FETCH_ASSOC);
	return $user;
}

//Рецистация пользователя(Добавление пользователя в таблицу Users)
function addUser($email , $password, $pdo)
{
	$sql ="INSERT INTO users (email,password) VALUES(:email, :password)";
	$statement = $pdo->prepare($sql);
	$result = $statement->execute([
		"email" => $email,
		"password" => password_hash($password , PASSWORD_DEFAULT)
	]);

	return $result;
}

//Установить сообщение для пользователя
function setFlashMessage($name , $message)
{
	if ($_SESSION[$name]) {
		$_SESSION[$name] = $message;
	}
}
//Показать сообщение пользователю
function displayFlashMessage($name){
	if (isset($_SESSION[$name])) {
		echo "<div class=\"alert alert-<?=$name?> text-dark\" role=\"alert\"><?=$_SESSION[$name]?></div>";
	}
	unset($_SESSION[$name]);
}

//Перенапраление пользователя
function redirectTo($path)
{
	header("location:$path");
}
