<?php
global $videos;
error_reporting(E_ALL);
ini_set('display_errors', 1);

//require_once 'header.php'; // подключение шапки
require_once 'db.php'; // подключение базы данных
require_once 'header.php';
require_once 'videos_handler.php';
function list_files($dir) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<li><a href='../$dir/$file' target='_blank'>$file</a></li>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Видео</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Загруженные видео</h1>
    <div class="videos">
        <?php if (!empty($videos)): ?>
            <?php foreach ($videos as $video): ?>
                <div class="video">
                    <video controls>
                        <source src="<?php echo htmlspecialchars($video['filepath']); ?>" type="<?php echo htmlspecialchars($video['filetype']); ?>">
                        Ваш браузер не поддерживает воспроизведение видео.
                    </video>
                    <p><?php echo htmlspecialchars($video['filename']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Видео не найдены.</p>
        <?php endif; ?>
    </div>
    <ul>
        <?php list_files('uploads/videos'); ?>
    </ul>
    <a href="index.php">Назад</a>
</body>
</html>

<?php require_once 'footer.php'?>