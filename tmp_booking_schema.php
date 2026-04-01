<?php
try {
    $dbh = new PDO('mysql:host=localhost;dbname=gymdb','root','');
    foreach ($dbh->query('SHOW COLUMNS FROM tblbooking') as $c) {
        echo $c['Field'] . "\t" . $c['Type'] . "\n";
    }
} catch (Exception $e) {
    echo 'ERR: ' . $e->getMessage();
}
