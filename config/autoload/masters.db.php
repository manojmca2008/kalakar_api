<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
return array(
    'db' => [
        'driver' => 'Pdo',
        'username' => 'manoj',
        'password' => 'abc@123',
        'dsn' => 'mysql:host=localhost;dbname=kalakar',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ],
    ],
);
