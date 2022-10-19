<?php
$host = "localhost";
$port = "5432";
$dbname = "szlaki";
$user = "postgres";
$password = "990316";
$connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} ";
$dbconn = pg_connect($connection_string);

$table_name = 'points';
?>
