<?php
$host = 'db'; // service name from docker-compose.yml
$user = 'devuser';
$password = 'devpass';
$db = 'test_db';

$conn = mysqli_connect($host,$user,$password,$db);
if (!$conn) {
    echo 'connection failed.' . mysqli_connect_error();
}
echo "Successfully connected to MySQL.";