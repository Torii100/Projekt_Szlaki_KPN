<?php
include_once('pg_connect.php');

if($_POST['id'] == -1){
    $sql = "INSERT INTO {$table_name}(name, coord, height) 
        VALUES 
           ('{$_POST['name']}', 
            'POINT({$_POST['latitude']} {$_POST['longitude']})', 
            '{$_POST['height']}')";

    pg_query($sql);

}else{
    $sql = "UPDATE {$table_name} 
        SET name='{$_POST['name']}',
            height='{$_POST['height']}',
            coord='POINT({$_POST['latitude']} {$_POST['longitude']})' 
        WHERE id={$_POST['id']}
    ";
    pg_query($sql);
}

$sql = "SELECT id, name, ST_X(coord::geometry) as latitude, 
       ST_Y(coord::geometry) as longitude, height  FROM {$table_name} WHERE name='{$_POST['name']}' 
        AND height='{$_POST['height']}'
        AND coord='POINT({$_POST['latitude']} {$_POST['longitude']})'";

$result = pg_query($sql);

$array = array();
while ($item = pg_fetch_object($result)) {
    $array[] = $item;
}
echo json_encode($array);

return;