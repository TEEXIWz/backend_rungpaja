<?php
    $servername = "202.28.34.197";
    $username = "web65_64011212136";
    $password = "64011212136@csmsu";
    $dbname = "web65_64011212136";

    $conn=new mysqli($servername,$username,$password,$dbname);
    $conn->set_charset("utf8");
    if ($conn->connect_error) {
        die("Connection Error". $conn->connect_error);
    }
?>