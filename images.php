<?php
global $pdo;
require_once 'header.php';
require_once 'db.php';
require_once 'images_handler.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Изображения</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <h1>Изображения</h1>
        <div class="gallery">
            <?php if ($images): ?>
                <?php foreach ($images as $image): ?>
                    <div class="image-item">
                        <img src="<?php echo htmlspecialchars($image['filepath'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($image['filename'], ENT_QUOTES, 'UTF-8'); ?>" />
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Нет изображений для отображения.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php
require_once 'footer.php';
?>

