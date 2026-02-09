<?php
include 'database.php';

$table = 'disposisi';
$result = mysqli_query($conn, "SHOW COLUMNS FROM $table");

if (!$result) {
    echo "Table '$table' does not exist or error: " . mysqli_error($conn);
} else {
    echo "Columns in '$table':<br>";
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
    }
    echo "</ul>";
}
?>
