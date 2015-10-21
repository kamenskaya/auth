<?php
namespace Wntrmn\Auth;

class Application
{
    private static $dbHost;
    private static $dbUser;
    private static $dbPassword;
    private static $dbName;

    private static $templatePath;

    private $templateVars = array();

    public function __construct()
    {
        self::$dbHost = Settings::DB_HOST;
        self::$dbUser = Settings::DB_USER;
        self::$dbPassword = Settings::DB_PASSWORD;
        self::$dbName = Settings::DB_NAME;

        self::$templatePath = $_SERVER['DOCUMENT_ROOT'] . Settings::TEMPLATE_PATH;
    }

    // main logics
    public function go()
    {
        try {

            // select visitor type (user or guest)
            if (self::CheckSession()) {
                $visitor = new User($_SESSION['user']['name']);

            } elseif (self::checkCookies()) {
                $visitor = new User($_COOKIE['name']);

            } else {
                $visitor = new Guest();
            }

            // select action
            $action = $visitor->getAction();

            // perform action
            if ($action) {

                switch ($action) {

                    case "login":
                        if ($visitor->auth()) {
                            $visitor = new User($visitor->getName());
                            $visitor->login();
                        }
                        break;

                    case "register":
                        if ($visitor->register()) {
                            $visitor = new User($visitor->getName());
                            $visitor->login();
                        }
                        break;

                    case "logout":
                        $visitor->logout();
                        break;

                    case "delete":
                        $visitor->remove();
                        break;
                }

                $this->reload();
            }

            // show html
            if ($visitor instanceof User) {

                $this->templateVars['name'] = $visitor->getName();
                $this->templateVars['id'] = $visitor->getId();
                $this->templateVars['regdate'] = $visitor->getRegDate();
                $this->templateVars['lastvisit'] = $visitor->getLastVisit();

                $this->showTemplate('inner');

            } elseif ($visitor instanceof Guest) {

                if (isset($_SESSION['message'])) {
                    $this->templateVars['message'] = $_SESSION['message'];
                    unset($_SESSION['message']);
                }

                if (isset($_SESSION['guest']['entered_name'])) {
                    $this->templateVars['entered_name'] = $_SESSION['guest']['entered_name'];
                    unset($_SESSION['guest']['entered_name']);
                }

                $this->showTemplate('login');

            } else {
                throw new \Exception();
            }

        } catch (\Exception $e) {
            $_SESSION['message'] = $e->getMessage();
            $this->reload();
        }
    }

    // connect to database
    public static function dbConnect()
    {
        $db = new \mysqli(self::$dbHost, self::$dbUser, self::$dbPassword, self::$dbName);
        if (mysqli_connect_errno()) {
            throw new \Exception(Messages::MYSQL_ERROR);
        }

        return $db;
    }

    // create token
    public static function createToken($userName)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $range = strlen($characters);
        $randomString = "";

        for ($i = 0; $i < 16; $i++) {
            $randomString .= $characters[rand(0, $range)];
        }

        $token = md5($userName . $randomString);
        return $token;
    }

    // check if session contains current visitor's information
    private static function checkSession()
    {
        $result = false;

        if (isset($_SESSION['user']['name'])) {
            $result = true;
        }

        return $result;
    }

    // check if cookies contain right name-token combination
    private static function checkCookies()
    {
        $result = false;

        if (isset($_COOKIE['name']) && isset($_COOKIE['token'])) {

            $db = self::dbConnect();

            $cookieName = $db->real_escape_string($_COOKIE['name']);
            $cookieToken = $db->real_escape_string($_COOKIE['token']);

            $query = "SELECT COUNT(*) FROM `users`
            WHERE `name` = '" . $cookieName . "' AND `token` = '" . $cookieToken . "'";
            $dbResult = $db->query($query);
            $row = $dbResult->fetch_assoc();

            if ($row['COUNT(*)'] > 0) {
                $result = true;
            }
        }

        return $result;
    }

    // show html template
    private function showTemplate($templateName)
    {
        $result = $this->templateVars;

        $path = self::$templatePath . $templateName . '.php';
        if (file_exists($path)) {
            include_once $path;
        } else {
            throw new \Exception(Messages::TEMPLATE_FILE_ERROR);
        }
    }

    // reload page
    private function reload()
    {
        header("Location: {$_SERVER['SCRIPT_NAME']}");
        exit();
    }

}
