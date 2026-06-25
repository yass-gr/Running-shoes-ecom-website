<?php

$env = parse_ini_file(__DIR__ . "/../.env");
$hostname = $env["DB_HOST"] ?? "localhost";
$dbname = $env["DB_NAME"] ?? "runningdb";
$username = $env["DB_USER"] ?? "g13";
$password = $env["DB_PASS"] ?? "yesnomaybeso";

try{
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

}catch(PDOException $e){
    echo "erreur : " . $e->getMessage();
}