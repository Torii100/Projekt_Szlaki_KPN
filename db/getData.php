<?php
include_once('pg_connect.php');

$sql = "SELECT id, name, ST_X(coord::geometry) as latitude, 
       ST_Y(coord::geometry) as longitude, height FROM {$table_name} ORDER BY id";

$result = pg_query($sql);
$array = array();

while ($item = pg_fetch_object($result)) {
    $array[] = $item;
}

echo json_encode($array);
return;