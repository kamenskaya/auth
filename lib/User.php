<?php
namespace Wntrmn\Auth;

class User extends Visitor
{
    private $id;
    private $name;
    private $regDate;
    private $lastVisit;

    public function __construct($userName)
    {
        if (isset($_SESSION['user'])) {

            $userData = $_SESSION['user'];

        } else {

            $userData = self::getUserData($userName);
            $_SESSION['user'] = $userData;
        }

        $this->id = $userData['id'];
        $this->name = $userData['name'];
        $this->regDate = $userData['regdate'];
        $this->lastVisit = $userData['lastvisit'];
    }

    // return user ID
    public function getId()
    {
        return $this->id;
    }

    // return user name
    public function getName()
    {
        return $this->name;
    }

    // return user registration date
    public function getRegDate()
    {
        return $this->regDate;
    }

    // return user last visit date
    public function getLastVisit()
    {
        return $this->lastVisit;
    }

    // return user action
    public function getAction()
    {
        $action = false;

        if (isset($_GET['action'])) {

            if ($_GET['action'] === 'logout') {
                $action = 'logout';
            } elseif ($_GET['action'] === 'delete') {
                $action = 'delete';
            }
        }

        return $action;
    }

    // login
    public function login()
    {
        $token = Application::createToken($this->name);
        $this->updateLastVisit();
        $this->updateToken($token);

        //set cookies
        setcookie('name', $this->name, time() + 60 * 60 * 24 * 14);
        setcookie('token', $token, time() + 60 * 60 * 24 * 14);
    }

    // logout
    public function logout()
    {
        // remove session data
        $_SESSION = array();
        unset ($_COOKIE[session_name()]);
        session_destroy();

        // remove cookies
        setcookie('name');
        setcookie('token');

        // delete token from database
        $this->clearToken();
    }

    // remove user
    public function remove()
    {
        // delete account from database
        $this->deleteAccount();

        // remove session data
        $_SESSION = array();
        unset ($_COOKIE[session_name()]);
        session_destroy();

        // remove cookies
        setcookie('name');
        setcookie('token');
    }

    // get user data from database
    private static function getUserData($userName)
    {
        $db = Application::dbConnect();

        $userName = $db->real_escape_string($userName);

        $query = "SELECT `id`, `name`, `regdate`, `lastvisit` FROM `users` WHERE `name` = '" . $userName . "'";
        $result = $db->query($query);
        $row = $result->fetch_assoc();

        return $row ? $row : false;
    }

    // set user last visit date
    private function updateLastVisit()
    {
        $db = Application::dbConnect();

        $id = intval($this->id);
        $lastVisit = date('Y-m-d H:i:s', time());

        $query = "UPDATE `users`
            SET `lastvisit` = '" . $lastVisit . "'
            WHERE `id` = " . $id;

        $db->query($query);
        if (mysqli_errno($db)) {
            throw new \Exception(Messages::MYSQL_ERROR);
        }
    }

    // update authentication token
    private function updateToken($token)
    {
        $db = Application::dbConnect();

        $id = intval($this->id);
        $token = $db->real_escape_string($token);

        $query = "UPDATE `users`
            SET `token` = '" . $token . "'
            WHERE `id` = " . $id;

        $db->query($query);
        if (mysqli_errno($db)) {
            throw new \Exception(Messages::MYSQL_ERROR);
        }

        return $token;
    }

    // clear authentication token
    private function clearToken()
    {
        $db = Application::dbConnect();

        $id = intval($this->id);

        $query = "UPDATE `users`
            SET `token` = NULL
            WHERE `id` = " . $id;

        $db->query($query);
        if (mysqli_errno($db)) {
            throw new \Exception(Messages::MYSQL_ERROR);
        }
    }

    // delete user account from database
    private function deleteAccount()
    {
        $db = Application::dbConnect();

        $id = intval($this->id);

        $query = "DELETE FROM `users`
            WHERE `id` = " . $id;

        $db->query($query);
        if (mysqli_errno($db)) {
            throw new \Exception(Messages::MYSQL_ERROR);
        }
    }

}
