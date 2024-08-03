<?php
global $pdo;
session_start();
require_once 'header.php';
require_once 'db.php';
require_once 'functions.php';  // Подключение файла с функциями

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploaded_file'])) {
    $username = $_SESSION['username'] ?? null;
    if (!$username) {
        echo "Ошибка: пользователь не авторизован.";
        exit;
    }

    $filename = $_FILES['uploaded_file']['name'];
    $temp_name = $_FILES['uploaded_file']['tmp_name'];
    $size = $_FILES['uploaded_file']['size'];

    if (empty($filename) || empty($temp_name)) {
        echo "Ошибка: файл не выбран.";
        exit;
    }

    $upload_dir = 'uploads/';
    ensure_upload_dir_exists($upload_dir);

    $unique_filename = generate_unique_filename($filename);
    $file_path = $upload_dir . $unique_filename;

    if (move_uploaded_file($temp_name, $file_path)) {
        if (check_file_type($file_path)) {
            log_upload($unique_filename, $username, $size, mime_content_type($file_path));
            try {
                $stmt = $pdo->prepare("INSERT INTO uploads_file (username, filename, filepath, filesize, filetype) VALUES (:username, :filename, :filepath, :filesize, :filetype)");
                $stmt->execute([
                    'username' => $username,
                    'filename' => $unique_filename,
                    'filepath' => $file_path,
                    'filesize' => $size,
                    'filetype' => mime_content_type($file_path)
                ]);
                echo "Файл успешно загружен.";
            } catch (PDOException $e) {
                echo "Ошибка при сохранении информации о файле в базе данных: " . $e->getMessage();
            }
        } else {
            echo "Ошибка: неподдерживаемый тип файла.";
        }
    } else {
        echo "Ошибка при загрузке файла.";
    }
} else {
    echo "Неверный метод запроса или файл не выбран.";
}

require_once 'footer.php';
?>


