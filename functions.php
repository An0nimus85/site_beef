<?php
// Функция для логирования загрузок файлов
// Функция для логирования загрузок файлов
function log_upload($filename, $username, $size, $type): void
{
    $log_file = 'logs/upload_log.txt';
    $current_time = date('Y-m-d H:i:s');
    $log_entry = "[$current_time] User '$username' uploaded file '$filename' (size: $size, type: $type)" . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Функция для проверки типа файла
function check_file_type($file_path) {
    $allowed_types = ['image/jpeg', 'image/png', 'video/mp4'];
    $file_type = mime_content_type($file_path);
    return in_array($file_type, $allowed_types);
}

// Функция для создания директории, если она не существует
function ensure_upload_dir_exists($dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Функция для создания уникального имени файла
function generate_unique_filename($filename) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $unique_name = uniqid() . '.' . $ext;
    return $unique_name;
}
?>
