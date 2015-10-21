<?php

namespace Wntrmn\Auth;

class Settings
{
    // database
    const DB_HOST = 'localhost';
    const DB_USER = 'user';
    const DB_PASSWORD = 'password';
    const DB_NAME = 'database';

    // paths
    const TEMPLATE_PATH = '/templates/';

    // validation rules
    const MAX_USERNAME_LENGH = 20;
    const MIN_PASSWORD_LENGH = 4;
    const MAX_PASSWORD_LENGH = 12;
}
