<?php
include_once('pg_connect.php');

$sql = "DELETE FROM {$table_name} WHERE id={$_POST['id']}";
echo pg_affected_rows(pg_query($sql));
return;