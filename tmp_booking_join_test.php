<?php
require 'include/config.php';
$sql = "SELECT t1.id as bookingid, t1.userid, t1.paymentType, t3.id as user_id, t3.fname, t3.email
FROM tblbooking as t1
LEFT JOIN tbluser as t3 ON t1.userid=t3.id
WHERE t1.paymentType IS NULL OR t1.paymentType='' LIMIT 10";
$stmt = $dbh->query($sql);
foreach ($stmt as $row) {
    echo "bookingid={$row['bookingid']} userid={$row['userid']} paymentType=".(isset($row['paymentType'])?$row['paymentType']:'NULL');
    echo " user_id=".(isset($row['user_id'])?$row['user_id']:'NULL');
    echo " fname=".(isset($row['fname'])?$row['fname']:'NULL');
    echo " email=".(isset($row['email'])?$row['email']:'NULL')."\n";
}
