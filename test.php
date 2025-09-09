<?php

use Doctrine\DBAL\Tools\DsnParser;

require_once 'vendor/autoload.php';


$parser = new DsnParser();

$url = $parser->parse('mysql://medical-app:medical-app@medical-app-dev-db:3306');

dd(strlen("def5020091c4a4ebd4d9da5bceccb059c6495f8e7fc28aa3a281e960535ecbc3b23cb83a5761d9801889c61e4139bacc9e15c78b4cde8e95a4d1ad0aeeffa42e850f58219f7f0585c7e00ddd2f01928d92d74de402d7db1f088696c9<ENC>"));