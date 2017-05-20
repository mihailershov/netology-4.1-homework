<?php

$host = 'localhost';
$dbname = 'ershov';
$dbport = 3306;

if ($_SERVER['HTTP_HOST'] == 'netology.dev') {
    $dbuser = 'mysql';
    $dbpassword = 'mysql';
}

if ($_SERVER['HTTP_HOST'] == 'university.netology.ru') {
    $dbuser = 'ershov';
    $dbpassword = 'neto1048';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbuser, $dbpassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $isbnQuery = $authorQuery = $booknameQuery = '';

    if (!empty($_GET['ISBN'])) {
        $isbn = $_GET['ISBN'];
        $isbnQuery = " WHERE isbn LIKE '%$isbn%'";
    }

    if (!empty($_GET['author'])) {
        $author = $_GET['author'];
        $queryPart = ' OR ';
        if (empty($isbn)) $queryPart = ' WHERE ';

        $authorQuery = "{$queryPart}author LIKE '%$author%'";
    }

    if (!empty($_GET['bookname'])) {
        $bookname = $_GET['bookname'];
        $queryPart = ' OR ';
        if (empty($isbn) && empty($author)) $queryPart = ' WHERE ';

        $booknameQuery = "{$queryPart}name LIKE '%$bookname%'";
    }

    $sqlQuery = "SELECT * FROM books{$isbnQuery}{$authorQuery}{$booknameQuery}";

    $statement = $pdo->prepare($sqlQuery);
    $statement->execute();
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
    <title>Document</title>
    <style>
        * {
            font-family: sans-serif;
        }

        .wrapper {
            max-width: 1440px;
            margin: auto;
        }

        table {
            border-collapse: collapse;
            border: 1px solid black;
            width: 100%;
        }

        td, tr {
            padding: 10px;
        }

        tr:nth-child(2n+1) {
            background-color: #eeeeee;
        }

        tr:first-child {
            background-color: lightgray;
            border-bottom: 1px solid black;
        }

        form > input {
            margin: 0 4px;
        }

        .form {
            margin: 20px auto;
            display: table;
        }
        ul>li {
            list-style-type: none;
            text-align: center;
        }
    </style>
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
            <input type="submit" value="Отфильтровать" name="filter">
            <button type="reset">Очистить форму</button>
        </form>
    </div>

    <?php if($statement->rowCount() === 0): ?>
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

    <?php if($statement->rowCount() !== 0): ?>
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
                <td><?php echo $row['name'] ?></td>
                <td><?php echo $row['author'] ?></td>
                <td><?php echo $row['year'] ?></td>
                <td><?php echo $row['genre'] ?></td>
                <td><?php echo $row['isbn'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
</body>
</html>

