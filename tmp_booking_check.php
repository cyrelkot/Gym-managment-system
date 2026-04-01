<?php
try {
    $dbh = new PDO('mysql:host=localhost;dbname=gymdb','root','');
    $stmt = $dbh->query('SELECT userid, COUNT(*) as cnt FROM tblbooking GROUP BY userid');
    foreach($stmt as $row) {
        echo "userid={$row['userid']} count={$row['cnt']}\n";
    }
} catch (Exception $e) {
    echo 'ERR: '.$e->getMessage();
}
