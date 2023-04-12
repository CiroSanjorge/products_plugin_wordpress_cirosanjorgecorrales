<?php
function connect_to_database($user, $pass, $server, $db)
{
    $user = "root";
    $pass = "";
    $server = "localhost";
    $db = "tienda_1";

    $connection = new mysqli($server, $user, $pass, $db);
    if ($connection->connect_errno) {
        return false;
    };
    return $connection;
}
function close_connection(mysqli $connection)
{
    $connection->close();
}
