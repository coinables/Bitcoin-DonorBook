<?php

//UPDATE THESE VARIABLES
$secret = "8cdPldf24f";   //CREATE A SECRET CODE FOR CALLBACK, MUST BE THE SAME ON THE CALLBACK PAGE
$my_address = "1J9ikqFuwrzPbczsDkquA9uVYeq6dEehsj"; //UPDATE TO YOUR BTC ADDRESS	
$siteRoot = "http://mysite.com/";  //CHANGE TO YOUR WEBSITE ROOT
$dbuser = "dbuser";          //CHANGE TO YOUR DB USERNAME
$dbpassword = "dbpassword";     //CHANGE TO YOUR DB PASSWORD
$dbname = "dbname";       //CHANGE TO THE NAME OF YOUR DB
	
//connect to the server
$conn = mysqli_connect("localhost", "$dbuser", "$dbpassword", "$dbname");
	if (mysqli_connect_errno()){
	echo "Connection to DB failed" . mysqli_connect_error();
	}	
	
if(isset($_POST['donateSubmit'])){
  //create unique postid
  $genR = uniqid();
  $postid = md5($genR);
  //check if empty
  if(!isset($_POST['donorName']) || trim($_POST['donorName']) ==''){
    $errMsg = "You must enter a name";
  } else if(!isset($_POST['donorNote']) || trim($_POST['donorNote']) ==''){
    $errMsg = "You must enter a note";
  } else {
  $donorName = $_POST['donorName'];
  $donorNote = $_POST['donorNote'];
  
  $donorName = mysqli_real_escape_string($conn, $donorName);
  $donorNote = mysqli_real_escape_string($conn, $donorNote);
  mysqli_query($conn, "INSERT INTO donate (postid, donor, note) VALUES ('$postid', '$donorName', '$donorNote')");
		//generate address
		$callback = $siteRoot."donorcallback.php?postid=".$postid."&secret=".$secret;
		$donResponse = json_decode(file_get_contents("https://blockchain.info/api/receive?method=create&address=".$my_address."&callback=".urlencode($callback)), true);
		$errMsg = "Please Send Donation to <a href='bitcoin:".$donResponse["input_address"]."'>".$donResponse["input_address"]."</a><br>
		<img src='http://chart.googleapis.com/chart?chs=125x125&cht=qr&chl=".$donResponse["input_address"]."' width='125'>";
		}
}
mysqli_close();

?>


<!DOCTYPE HTML>
<html>
<title>DonorBook</title>

<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Sans">
<style>
html { 
  background-color: #ffffff;
  margin: 0;
  padding: 0;
  color: #121212;
  font-family: "Droid Sans";
}
#donations{
	border: 2px solid #5C8AE6;
	border-radius: 3px;
	font-family: "Droid Sans";
}
#donateForm{
  display: block;
}
#donorName{
  height: 28px;
}
.titleF {
	font-weight: bold;
	font-size: 24px;
}
.donateR {
	background-color: #5C8AE6;
	color: #ffffff;
	font-family: "Droid Sans";
}
.donateBorder{
  border-bottom: 1px dotted #c3c3c3;
}
#donorNote{
  height: 28px;  
}
#donateSubmit{
    height: 34px;
	color: #ffffff;
	background-color: #5C8AE6;
}
#donorTxt{
  font-size: 12px;
}
</style>
<table id="donations" width="85%">
<tr><td colspan="3"><span class="titleF">Donor Book</span></td></tr>
<tr>
<td class="donateR">Donor</td>
<td class="donateR">Note</td>
<td class="donateR" width="150">Amount</td>
</tr>
<?php 

$query ="SELECT * FROM donate WHERE paid = 'Y' ORDER BY postn DESC LIMIT 10";
$result=mysqli_query($conn, $query);
while($outputs=mysqli_fetch_assoc($result)){
    echo "<tr>";
	echo "<td class='donateBorder'>".$outputs['donor']."</td>";
	echo "<td class='donateBorder'>".$outputs['note']."</td>";
	$amountCon = $outputs['amount'] / 100000000;
	echo "<td class='donateBorder'>".$amountCon." BTC</td>";
	echo "</tr>";
}	

mysqli_close();

?>
</table><br><span id="donorTxt">You can post to the donor book too! Fill out the below and send a few bits.</span><br>
<script>
function noteLimit(element, stopAt)
{
    var max_chars = stopAt;

    if(element.value.length > max_chars) {
        element.value = element.value.substr(0, max_chars);
    }
}
</script>
<form id="donateForm" method="post" action="#donateForm">
<input type="text" name="donorName" id="donorName" placeholder="Name" maxlength="20" onkeydown="noteLimit(this, 20);" onkeyup="noteLimit(this, 20);">
<input type="text" name="donorNote" id="donorNote" placeholder="Note up to 120 characters" size="40" maxlength="120" onkeydown="noteLimit(this, 120);" onkeyup="noteLimit(this, 120);">
<input type="submit" name="donateSubmit" id="donateSubmit" value="Donate">
</form>
<?php echo $errMsg; ?>
<br>
</html>