<?php
header('Content-Type: text/plain');
include 'database.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$db_name_result = mysqli_query($conn, "SELECT DATABASE()");
$db_name = mysqli_fetch_array($db_name_result)[0];
echo "Database: " . $db_name . "\n\n";

echo "Tables:\n";
$tables = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_array($tables)) {
    echo "- " . $row[0] . "\n";
}

echo "\nUsers:\n";
$query = "SELECT * FROM user";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Query failed: " . mysqli_error($conn) . "\n";
} else {
    echo "Count: " . mysqli_num_rows($result) . "\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "No: " . $row['no'] . ", Username: " . $row['username'] . ", Role: " . $row['role'] . ", Bidang: " . ($row['nama_bidang'] ? $row['nama_bidang'] : 'NULL') . "\n";
    }
}
?>
