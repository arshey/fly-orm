<?php
require "vendor/autoload.php";

use Src\orm\Fly;

$fly = Fly::setup([


'TYPE'          => 'mysql',
'HOSTNAME'      => 'localhost',
'DBNAME'        => 'test',
'USERNAME'      => 'root',
'PASSWORD'      => 'root'

]);

// Recuperation de données

$cars = $fly->table('cars');

$all = $cars->get();

// 