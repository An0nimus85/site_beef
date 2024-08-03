<?php
global $pdo;
require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT * FROM uploads_file WHERE filetype LIKE 'image/%'");
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Ошибка при извлечении изображений: " . $e->getMessage());
    $images = [];
}
?>


