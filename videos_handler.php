<?php
global $pdo;
require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT * FROM uploads WHERE filetype LIKE 'video/%'");
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Ошибка при извлечении видео: " . $e->getMessage());
    $videos = [];
}
?>
