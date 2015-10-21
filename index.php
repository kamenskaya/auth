<?php
namespace Wntrmn\Auth;
session_start();

require_once "lib/Application.php";
require_once "lib/Settings.php";
require_once "lib/Messages.php";
require_once "lib/Visitor.php";
require_once "lib/Guest.php";
require_once "lib/User.php";

$app = new Application;
$app->go();
