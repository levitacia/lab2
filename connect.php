<?php

    $connect = mysqli_connect('localhost', 'volodya', 'volodya', 'lab02');

    if (!$connect) {
        die('Error connect to DataBase');
    }