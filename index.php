<?php
$host = 'localhost';
$dbname = 'ershov';
$dbuser = 'ershov';
$dbpassword = 'neto1048';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbuser, $dbpassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $isbnQuery = $authorQuery = $booknameQuery = '';
    $varsArray = [];

    if (!empty($_GET['ISBN'])) {
        $isbn = trim($_GET['ISBN']);
        $varsArray[] = "%$isbn%";
        $isbnQuery = " WHERE isbn LIKE ?";
    }
    if (!empty($_GET['author'])) {
        $author = trim($_GET['author']);
        $varsArray[] = "%$author%";
        $queryPart = ' OR ';
        if (empty($isbn)) $queryPart = ' WHERE ';
        $authorQuery = "{$queryPart}author LIKE ?";
    }
    if (!empty($_GET['bookname'])) {
        $bookname = trim($_GET['bookname']);
        $varsArray[] = "%$bookname%";
        $queryPart = ' OR ';
        if (empty($isbn) && empty($author)) $queryPart = ' WHERE ';
        $booknameQuery = "{$queryPart}name LIKE ?";
    }
    $sqlQuery = "SELECT * FROM `books`{$isbnQuery}{$authorQuery}{$booknameQuery}";
    $statement = $pdo->prepare($sqlQuery);

    if (!$varsArray) {
        $statement->execute();
    } else {
        $statement->execute($varsArray);
    }

} catch (PDOException $e) {
    die($e->getMessage());
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="index.css">
    <title>4.1-homework</title>
</head>
<body>
<div class="wrapper">
    <div class="form">
        <form method="GET">
            <input type="text" name="ISBN" placeholder="ISBN" id="ISBN" value="<?php if (!empty($isbn)) echo $isbn ?>">
            <input type="text" name="author" placeholder="Автор книги" id="author"
                   value="<?php if (!empty($author)) echo $author ?>">
            <input type="text" name="bookname" placeholder="Название книги" id="bookname"
                   value="<?php if (!empty($bookname)) echo $bookname ?>">
            <input type="submit" value="Отфильтровать">
        </form>
    </div>

    <?php if ($statement->rowCount() === 0): ?>
        <a href="index.php">Назад</a>
        <ul>
            <?php if (!empty($isbn) && $statement->rowCount() === 0): ?>
                <li>По фильтру ISBN ничего не найдено</li>
            <?php endif; ?>
            <?php if (!empty($author) && $statement->rowCount() === 0): ?>
                <li>По фильтру author ничего не найдено</li>
            <?php endif; ?>
            <?php if (!empty($bookname) && $statement->rowCount() === 0): ?>
                <li>По фильтру bookname ничего не найдено</li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>

    <?php if ($statement->rowCount() !== 0): ?>
        <table>
            <tr>
                <td>Название</td>
                <td>Автор</td>
                <td>Год</td>
                <td>Жанр</td>
                <td>ISBN</td>
            </tr>
            <?php foreach ($statement as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']) ?></td>
                    <td><?php echo htmlspecialchars($row['author']) ?></td>
                    <td><?php echo htmlspecialchars($row['year']) ?></td>
                    <td><?php echo htmlspecialchars($row['genre']) ?></td>
                    <td><?php echo htmlspecialchars($row['isbn']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>