<!DOCTYPE html>
<html>
<head>
    <title>Demo auth</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?= Wntrmn\Auth\Settings::TEMPLATE_PATH ?>css/styles.css">
</head>
<body>
<div class="page-container">
    <p>You logged in as <?= $result['name'] ?></p>

    <p>ID: <?= $result['id'] ?></p>

    <p>Registration date: <?= $result['regdate'] ?></p>

    <p>Last visit: <?= $result['lastvisit'] ?></p>
    <br/>

    <p><a href="<?= $_SERVER['SCRIPT_NAME'] ?>?action=logout">Logout</a></p>

    <p><a href="<?= $_SERVER['SCRIPT_NAME'] ?>?action=delete">Delete account</a></p>
</div>
</body>
</html>
