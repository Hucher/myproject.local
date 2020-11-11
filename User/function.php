<?php
$pdo = new PDO('mysql:host=myProject.local;dbname=myproject', 'root', 'root');

//Получить пользователя по Еmail
function getEmailUser($pdo, $email)
{
    $sql = "SELECT email FROM users WHERE :email=email";

    $statement = $pdo->prepare($sql);
    $statement->execute(['email' => $email]);

    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user;
}

//Получить авторизованного пользователя
function getAuthUser($pdo, $email, $password)
{
    $sql = "SELECT * FROM users WHERE :email=email";

    $statement = $pdo->prepare($sql);
    $statement->execute([
        'email' => $email,
    ]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    if ($user !== false && password_verify($password, $user['password'])) {
        return $user;
    } else {
        return false;
    }
}

//Регистация пользователя(Добавление пользователя в таблицу Users)
function addUser($email, $password, $pdo)
{
    $sql = "INSERT INTO users (email,password) VALUES(:email, :password)";
    $statement = $pdo->prepare($sql);
    $result = $statement->execute([
        "email" => $email,
        "password" => password_hash($password, PASSWORD_DEFAULT)
    ]);

    return $result;
}

//Установить сообщение для пользователя
function setFlashMessage($name, $message)
{
    if ($_SESSION[$name]) {
        $_SESSION[$name] = $message;
    }
}

//Показать сообщение пользователю
function displayFlashMessage($name)
{
    if (isset($_SESSION[$name])) {
        echo "<div class=\"alert alert-$name text-dark\" role=\"alert\">$_SESSION[$name]</div>";
    }
    unset($_SESSION[$name]);
}

//Перенапраление пользователя
function redirectTo($path)
{
    header("location:$path");
    exit();
}
//Функция проверки не, на авторизованного пользователя
function isNotLoggedIn()
{
    if (empty($_SESSION['infoUser'])) {
        $_SESSION['warning'] = 'Не верные Email или Password.';
        setFlashMessage('warning', $_SESSION['warning']);
        return true;
    }
    return false;
}

//Получить всех пользователей
function getAllUsers($pdo){
    $sql = "SELECT * FROM users";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $users = $statement->FetchAll(PDO::FETCH_ASSOC);
    return $users;
}