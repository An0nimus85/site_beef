<?php
// Параметры подключения к базе данных
$host = 'localhost'; // адрес сервера базы данных (обычно localhost)
$dbname = 'logs_db'; // имя базы данных
$username = 'root'; // имя пользователя для подключения к базе данных
$password = '19Veronik@85'; // пароль для подключения к базе данных


try {
    // Подключение к базе данных
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Установка режима ошибок PDO на исключения
    $pdo->setAttribute(attribute: PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION);
    
    // Если нужно использовать подготовленные запросы, можно это сделать здесь
    
} catch (PDOException $e) {
    // В случае ошибки выводим сообщение
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Функция для логирования посещений
function log_visit($pdo) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $url = $_SERVER['REQUEST_URI'];

    // Подготовка SQL-запроса для вставки данных
    $stmt = $pdo->prepare("INSERT INTO visits (ip_address, url) VALUES (:ip_address, :url)");
    $stmt->execute(['ip_address' => $ip_address, 'url' => $url]);
}
?>
