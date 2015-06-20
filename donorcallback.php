<?php
$secret = "8cdPldf24f"; //CREATE A SECRET CODE FOR CALLBACK, MUST BE THE SAME ON THE DONORBOOK PAGE
$dbuser = "dbuser";          //CHANGE TO YOUR DB USERNAME
$dbpassword = "dbpassword";     //CHANGE TO YOUR DB PASSWORD
$dbname = "dbname";       //CHANGE TO THE NAME OF YOUR DB

//check if request is from bc.info
if($_GET['secret'] != $secret){
 echo "Quit hacking hacker!";
 return;
}

//connect to the server
$conn = mysqli_connect("localhost", "$dbuser", "$dbpassword", "$dbname");
	if (mysqli_connect_errno()){
	echo "Connection to DB failed" . mysqli_connect_error();
	}	

$posterid = $_GET['postid'];
$donateAmount = $_GET['value'];
//update paid
	mysqli_query($conn, "UPDATE donate SET paid = 'Y' WHERE postid = '$posterid'");
//update amount
    mysqli_query($conn, "UPDATE donate SET amount = '$donateAmount' WHERE postid = '$posterid'");

//tell blochchain.info that we received the callback
echo "*ok*";


?>