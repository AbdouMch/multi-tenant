<?php

use Doctrine\DBAL\Tools\DsnParser;

require_once 'vendor/autoload.php';


$parser = new DsnParser();

$url = $parser->parse('mysql://medical-app:medical-app@medical-app-dev-db:3306');

dd($url);