<?php
namespace Wntrmn\Auth;

class Guest extends Visitor
{
    private $enteredName;
    private $enteredPassword;

    public function __construct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->enteredName = trim(htmlspecialchars($_POST['name']));
            $this->enteredPassword = trim(htmlspecialchars($_POST['password']));
        }
    }

    // return entered name
    public function getName()
    {
        return $this->enteredName;
    }

    // return guest action
    public function getAction()
    {
        $action = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['login'])) {
                $action = 'login';
            } elseif (isset($_POST['register'])) {
                $action = 'register';
            }
        }

        return $action;
    }

    // Authenticate
    public function auth()
    {
        $result = false;

        $_SESSION['guest']['entered_name'] = $this->enteredName;

        // check entered data
        $nameCorrect = $this->checkNameValid() && $this->checkUserNameExists();
        $passwordCorrect = $this->checkPasswordMatch();

        if ($nameCorrect) {

            if ($passwordCorrect) {

                $result = true;

            } else {
                $_SESSION['message'] = Messages::PASSWORD_WRONG;
            }
        } else {
            $_SESSION['message'] = Messages::USER_NOT_EXIST;
        }

        return $result;
    }

    // Register
    public function register()
    {
        $result = false;

        $_SESSION['guest']['entered_name'] = $this->enteredName;

        // check data
        $nameValid = $this->checkNameValid();
        $userNameExists = $this->checkUserNameExists();
        $passwordValid = $this->checkPasswordValid();
        $samePasswordName = $this->enteredPassword === $this->enteredName;

        if ($nameValid) {

            if (!$userNameExists) {

                if ($passwordValid) {

                    if (!$samePasswordName) {

                        // add user account
                        $this->addAccount();
                        $result = true;

                    } else {
                        $_SESSION['message'] = Messages::SAME_PASSWORD_NAME;
                    }
                }
            } else {
                $_SESSION['message'] = Messages::USER_EXISTS;
            }
        }

        return $result;
    }

    // check if name is valid
    private function checkNameValid()
    {
        $result = true;

        $enteredName = $this->enteredName;
        $nameLong = strlen($enteredName) > Settings::MAX_USERNAME_LENGH;

        if (empty($enteredName)) {
            $_SESSION['message'] = Messages::NAME_REQUIRED;
            $result = false;
        } elseif (preg_match('/[^[:alnum:]]/', $enteredName)) {
            $_SESSION['message'] = Messages::NAME_INVALID;
            $result = false;
        } elseif ($nameLong) {
            $_SESSION['message'] = Messages::NAME_LONG;
            $result = false;
        }

        return $result;
    }

    // check if password is valid
    private function checkPasswordValid()
    {
        $result = true;

        $enteredPassword = $this->enteredPassword;

        $passwordShort = !empty($enteredPassword) && strlen($enteredPassword) < Settings::MIN_PASSWORD_LENGH;
        $passwordLong = strlen($enteredPassword) > Settings::MAX_PASSWORD_LENGH;

        if (empty($enteredPassword)) {
            $_SESSION['message'] = Messages::PASSWORD_REQUIRED;
            $result = false;
        } elseif (preg_match('/[^[:alnum:]]/', $enteredPassword)) {
            $_SESSION['message'] = Messages::PASSWORD_INVALID;
            $result = false;
        } elseif ($passwordShort) {
            $_SESSION['message'] = Messages::PASSWORD_SHORT;
            $result = false;
        } elseif ($passwordLong) {
            $_SESSION['message'] = Messages::PASSWORD_LONG;
            $result = false;
        }

        return $result;
    }

    // check if this username already exists
    private function checkUserNameExists()
    {
        $result = false;

        $db = Application::dbConnect();

        $enteredName = $db->real_escape_string($this->enteredName);

        $query = "SELECT COUNT(*) FROM `users` WHERE `name` = '" . $enteredName . "'";
        $dbResult = $db->query($query);
        $row = $dbResult->fetch_assoc();

        if ($row['COUNT(*)'] > 0) {
            $result = true;
        }

        return $result;
    }

    // check password match
    private function checkPasswordMatch()
    {
        $result = false;

        $db = Application::dbConnect();

        $enteredName = $db->real_escape_string($this->enteredName);
        $passwordHash = md5($this->enteredPassword);

        $query = "SELECT COUNT(*) FROM `users`
            WHERE `name` = '" . $enteredName . "' AND `password` = '" . $passwordHash . "'";
        $dbResult = $db->query($query);
        $row = $dbResult->fetch_assoc();

        if ($row['COUNT(*)'] > 0) {
            $result = true;
        }

        return $result;
    }

    // add account to database
    private function addAccount()
    {
        $db = Application::dbConnect();

        $enteredName = $db->real_escape_string($this->enteredName);
        $passwordHash = md5($this->enteredPassword);
        $regdate = date('Y-m-d H:i:s', time());
        $lastVisit = $regdate;

        $query = "INSERT INTO `users` (`name`, `password`, `regdate`, `lastvisit`) VALUES (
						'" . $enteredName . "',
						'" . $passwordHash . "',
						'" . $regdate . "',
						'" . $lastVisit . "'
						)";

        $db->query($query);
        if (mysqli_errno($db)) {
            throw new \Exception(Messages::MYSQL_ERROR);
        }
    }

}
