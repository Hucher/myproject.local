<?php
$pdo = new PDO('mysql:host=myProject.local;dbname=myproject', 'root', 'root');

//Отладочная функция
function dd(...$data)
{
    echo '<pre>';
    var_dump($data);
    echo '<pre>';
    die();
}

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
function getAllUsers($pdo)
{
//    $sql = "SELECT * FROM users";
    $sql = "SELECT * FROM users join (media,general_information) USING (media_id,inform_id)";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $users = $statement->FetchAll(PDO::FETCH_ASSOC);
    return $users;
}

//Добавить пользователя
function add_User($email, $password, $pdo)
{
    $sql = "INSERT INTO users (email,password) VALUES (:email ,:password)";
    $statement = $pdo->prepare($sql);
    $statement->execute([
        "email" => $email,
        "password" => password_hash($password, PASSWORD_DEFAULT)
    ]);
    $userId = $pdo->lastInsertId();
    return (int)$userId;
}

//записать общую информацию
function editInformation($userId, $userName, $position, $phone, $address, $pdo)
{
    $sql = "INSERT INTO general_information (name, position ,phone ,address,user_id) VALUES (:name ,:position ,:phone ,:address,:user_id)";

    $statement = $pdo->prepare($sql);
    $result = $statement->execute([
        'name' => $userName,
        'position' => $position,
        'phone' => $phone,
        'address' => $address,
        'user_id' => $userId
    ]);
    $informId = $pdo->lastInsertId('general_information');
    $sql2 = "UPDATE users SET inform_id=:inform_id WHERE user_id=:user_id";
    $statement2 = $pdo->prepare($sql2);
    $statement2->execute([
        'inform_id' => $informId,
        'user_id' => $userId
    ]);

    return true;
}

//Установить статус
function setStatus($status, $userId, $pdo)
{
    $sql = "INSERT INTO media (status,user_id) VALUES (:status ,:user_id)";
    $statement = $pdo->prepare($sql);
    $statement->execute([
        'status' => $status,
        'user_id' => $userId
    ]);
    $mediaId = $pdo->lastInsertId('media');

    $sql2 = "UPDATE users SET media_id=:media_id WHERE user_id=:user_id";
    $statement2 = $pdo->prepare($sql2);
    $statement2->execute([
        'media_id' => $mediaId,
        'user_id' => $userId
    ]);
    return $mediaId;
}

//Загрузить аватар профиля
function uploadImage($fileTmp, $image, $pdo, $mediaId)
{
    $upload = move_uploaded_file($fileTmp, "img/demo/avatars/" . $image);
    $image = 'img/demo/avatars/' . $image;
    if (!empty($mediaId && !empty($upload))) {
        $sql = 'UPDATE media SET image=:image WHERE media_id=:media_id';
        $statement = $pdo->prepare($sql);
        $statement->execute([
            'image' => $image,
            'media_id' => $mediaId
        ]);
        return true;
    }
    return false;
}

//Записать ссылки на соц сети
function addSocialLinks($mediaId, $telegram, $instagram, $vk, $pdo)
{
    $sql = 'UPDATE media SET telegram=:telegram,vk=:vk,instagram=:instagram WHERE media_id=:media_id';
    $statement = $pdo->prepare($sql);
    $statement->execute([
        'telegram' => $telegram,
        'instagram' => $instagram,
        'vk' => $vk,
        'media_id' => $mediaId
    ]);
}

//Получить пользователя по его ID
function getUserById($user_id, $pdo)
{
    $sql = "SELECT * FROM users JOIN (general_information,media) 
            on users.user_id=general_information.user_id 
            and users.user_id=media.user_id WHERE users.user_id=:user_id";

    $statement = $pdo->prepare($sql);
    $statement->execute(['user_id' => $user_id]);

    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user;
}

//Редактирование общей информации
function updateInfo($userId, $userName, $position, $phone, $address, $pdo)
{
    $sql = "UPDATE general_information SET name=:name,position=:position,phone=:phone,address=:address WHERE user_id=:user_id";

    $statement = $pdo->prepare($sql);
    $result = $statement->execute([
        'name' => $userName,
        'position' => $position,
        'phone' => $phone,
        'address' => $address,
        'user_id' => $userId
    ]);
    return true;
}

//Проверка на автора (профиля)
function isAuthor($loggedUserId, $editUserId)
{
    if ($loggedUserId !== $editUserId) {
        return false;
    }
    return true;
}

//Обновление учетных данных авторизованного пользователя
function updateAuthUser($userId, $email, $password, $pdo)
{
    $sql = "UPDATE users SET email=:email,password=:password WHERE user_id=:user_id";
    $statement = $pdo->prepare($sql);
    $user = $statement->execute([
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'user_id' => $userId
    ]);
    return $user;
}

//Изменить статус
function updateStatus($userId, $status,$pdo){
    $sql = 'UPDATE media SET status=:status WHERE user_id=:user_id';
    $statement = $pdo->prepare($sql);
    $status = $statement->execute([
        'user_id' => $userId,
        'status' => $status
    ]);
    return $status;
}

//Имеется картинка у пользователя
function hasImage(array $user){

    if (!empty($user['image'])){
        return true;
    }
    return false;
}
function deleteUser($pdo ,$userId){
    $sql = "DELETE FROM users WHERE user_id=:user_id";
    $statement = $pdo->prepare($sql);
    $result = $statement->execute([
        'user_id' => $userId
    ]);
    return $result;
}