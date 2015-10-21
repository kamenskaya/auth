<!DOCTYPE html>
<html>
<head>
    <title>Demo auth</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?= Wntrmn\Auth\Settings::TEMPLATE_PATH ?>css/styles.css">
</head>
<body>
<div class="center">
    <div class="login-form">
        <form action="<?= $_SERVER['SCRIPT_NAME'] ?>" method="POST">
            <table>
                <tr>
                    <td>Login:</td>
                    <td><input type="text" name="name" value="<?= $result['entered_name'] ?>"></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="password"></td>
                </tr>
            </table>
            <div class="message">
                <p><?= $result['message'] ?></p>
            </div>
            <div class="buttons">
                <table>
                    <tr>
                        <td><input type="submit" name="login" value="Login" class="submit-button"></td>
                        <td><input type="submit" name="register" value="Register" class="submit-button"></td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
</div>
</body>
</html>



