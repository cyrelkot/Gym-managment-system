<?php
try {
    $dbh = new PDO('mysql:host=localhost;dbname=gymdb','root','');
    $count = $dbh->query('SELECT COUNT(*) FROM tbluser')->fetchColumn();
    echo "users=$count\n";
} catch (Exception $e) {
    echo 'ERR: ' . $e->getMessage();
}
