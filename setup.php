<?php
$servername = "localhost";
$username = "username"; //Your DB Username
$password = "password"; //your DB password
$dbname = "dbname";     //The name of your DB

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create new table called donate
$sql = "CREATE TABLE donate (
postn INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
postid VARCHAR(50) NOT NULL,
paid VARCHAR(1) DEFAULT'N' NOT NULL,
amount INT(30),
donor VARCHAR(20) NOT NULL,
note VARCHAR(150)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>