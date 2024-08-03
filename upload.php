<?php
global$pdo; require_once 'db.php'; // Подключаем файл с настройками базы данных
require_once  'header.php';
require_once 'functions.php';
$user_id = $_SESSION['user_id'] ?? null; // Проверяем, авторизован ли пользователь
error_reporting(E_ALL);
ini_set('display_errors', 1);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploaded_file'])) {
    $username = $_SESSION['username'] ?? null;
    if (!$username) {
        echo "Ошибка: пользователь не авторизован.";
        exit;
    }

    $filename = $_FILES['uploaded_file']['name'];
    $temp_name = $_FILES['uploaded_file']['tmp_name'];
    $size = $_FILES['uploaded_file']['size'];
    $error = $_FILES['uploaded_file']['error'];

    if ($error !== UPLOAD_ERR_OK) {
        echo "Ошибка при загрузке файла. Код ошибки: $error";
        exit;
    }

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
            $file_mime_type = mime_content_type($file_path);

            try {
                $stmt = $pdo->prepare("INSERT INTO uploads_file (username, filename, filepath, filesize, filetype) VALUES (:username, :filename, :filepath, :filesize, :filetype)");
                $stmt->execute([
                    'username' => $username,
                    'filename' => $unique_filename,
                    'filepath' => $file_path,
                    'filesize' => $size,
                    'filetype' => $file_mime_type
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

if (!$user_id) {
    die('Вы должны быть авторизованы для загрузки файлов.');
}
function log_message($message) {
    $log_file = 'logs/upload_log.txt';
    $current_time = date('Y-m-d H:i:s');
    $log_entry = "[$current_time] $message" . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

function is_valid_file($file_path, $mime_type, $allowed_types) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $detected_type = finfo_file($finfo, $file_path);
    finfo_close($finfo);

    return in_array($detected_type, $allowed_types) && $detected_type === $mime_type;
}

function check_forbidden_content($file_contents) {
    $forbidden_patterns = [
        'PHP script tag' => '/<\?php/',
        'HTML script tag' => '/<script/'
    ];

    foreach ($forbidden_patterns as $description => $pattern) {
        if (preg_match($pattern, $file_contents)) {
            return $description;
        }
    }

    return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $file_name = basename($file['name']);
    $file_tmp = $file['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $file_mime = mime_content_type($file_tmp);

    // Проверка на ошибки загрузки
    if ($file['error'] != UPLOAD_ERR_OK) {
        log_message("Ошибка при загрузке файла '$file_name' пользователем $user_id: " . $file['error']);
        die('Ошибка при загрузке файла.');
    }

    // Определяем папку для загрузки файла и допустимые MIME-типы
    $upload_dir = '';
    $allowed_types = [];
    if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
        $upload_dir = 'uploads/images/';
        $file_type = 'image';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    } elseif (in_array($file_ext, ['mp4', 'avi', 'mov'])) {
        $upload_dir = 'uploads/videos/';
        $file_type = 'video';
        $allowed_types = ['video/mp4', 'video/x-msvideo', 'video/quicktime'];
    } else {
        $upload_dir = 'uploads/others/';
        $file_type = 'other';
        $allowed_types = ['application/pdf', 'text/plain', 'application/zip'];
    }

    // Проверка MIME-типа файла
    if (!is_valid_file($file_tmp, $file_mime, $allowed_types)) {
        log_message("Пользователь $user_id пытался загрузить файл '$file_name' с недопустимым MIME-типом '$file_mime'. Обнаружено при проверке MIME-типа.");
        die('Недопустимый тип файла.');
    }

    // Проверка на наличие вредоносных скриптов
    $file_contents = file_get_contents($file_tmp);
    $forbidden_content = check_forbidden_content($file_contents);
    if ($forbidden_content) {
        log_message("Пользователь $user_id пытался загрузить файл '$file_name', содержащий запрещенный контент: $forbidden_content. Обнаружено при проверке содержимого файла.");
        die('Файл содержит запрещенные скрипты.');
    }

    // Создаем папку, если она не существует
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            log_message("Ошибка при создании директории '$upload_dir' для пользователя $user_id.");
            die('Ошибка при создании директории для загрузки файла.');
        }
    }

    // Полный путь к файлу
    $file_path = $upload_dir . $file_name;

    // Перемещаем файл в нужную папку
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Сохраняем информацию о файле в базе данных
        try {
            $stmt = $pdo->prepare("INSERT INTO uploads (user_id, file_name, file_type, file_path) VALUES (:user_id, :file_name, :file_type, :file_path)");
            $stmt->execute(['user_id' => $user_id, 'file_name' => $file_name, 'file_type' => $file_type, 'file_path' => $file_path]);
            log_message("Пользователь $user_id успешно загрузил файл '$file_name' в '$file_path'.");
            echo 'Файл успешно загружен!';
        } catch (PDOException $e) {
            log_message("Ошибка при сохранении информации о файле '$file_name' в базе данных для пользователя $user_id: " . $e->getMessage());
            die('Ошибка при сохранении информации о файле в базе данных.');
        }
    } else {
        log_message("Ошибка при перемещении файла '$file_name' в '$file_path' пользователем $user_id.");
        die('Ошибка при загрузке файла.');
    }
} else {
    echo 'Выберите файл для загрузки.';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Загрузка файла</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Загрузка файла</h1>
    <form action="upload_handler.php" method="post" enctype="multipart/form-data">
        <label for="uploaded_file">Выберите файл:</label>
        <input type="file" name="uploaded_file" id="uploaded_file" required>
        <button type="submit">Загрузить</button>
    </form>
    <a href="images.php">Изображения</a> | <a href="videos.php">Видео</a>
</body>
</html>
<?php
require_once 'footer.php';
?>