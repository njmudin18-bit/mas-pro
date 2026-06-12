<?php
$serverName = "10.11.9.22, 1433";
$connectionInfo = array( "Database"=>"Attendance", "UID"=>"sa", "PWD"=>"Miyako2001");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Connection established.\n";
}else{
     echo "Connection could not be established.\n";
     die( print_r( sqlsrv_errors(), true));
}

$sql = "SELECT * FROM DEPARTMENTS WHERE DEPTNAME LIKE '%GA%' OR DEPTNAME LIKE '%GENERAL%'";
$stmt = sqlsrv_query( $conn, $sql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}

while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
      echo $row['DEPTID'].", ".$row['DEPTNAME']."\n";
}

sqlsrv_free_stmt( $stmt);
sqlsrv_close( $conn);
?>
