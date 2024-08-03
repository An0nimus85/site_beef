
<?php include 'header.php'; ?>
<h1>Добро пожаловать на мой блог!</h1>
    <p>Здесь вы можете загружать и просматривать файлы.</p>
<form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="file">Загрузите файл:</label>
        <input type="file" name="file" id="file">
        <button type="submit">Загрузить</button>
    </form>
<?php include 'footer.php'; ?>
