<?php
header('Content-Type: text/plain');
include 'database.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Database: " . mysqli_fetch_array(mysqli_query($conn, "SELECT DATABASE()"))[0] . "\n\n";

echo "Tables:\n";
$tables = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_array($tables)) {
    echo "- " . $row[0] . "\n";
}

echo "\nUsers:\n";
$query = "SELECT id, username, role, nama_bidang FROM user";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Query failed: " . mysqli_error($conn) . "\n";
} else {
    echo "Count: " . mysqli_num_rows($result) . "\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo sprintf("[%s] %s (%s) - Bidang: %s\n", 
            $row['id'], $row['username'], $row['role'], $row['nama_bidang'] ? $row['nama_bidang'] : 'NULL');
    }
}
?>
