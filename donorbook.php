<?php

//UPDATE THESE VARIABLES
$secret = "8cdPldf24f";   //CREATE A SECRET CODE FOR CALLBACK, MUST BE THE SAME ON THE CALLBACK PAGE
$api_key = "asdf321-asdf321-df54f2";  //Your blockchain.info receive payments API key
$xpub = "xpub661MyMwAqRbcFtXgS5sYJABqqG9YLmC4Q1Rdap9gSE8NqtwybGhePY2gZ29ESFjqJoCu1Rupje8YtGqsefD265TMg7usUDFdp6W1EGMcet8"; //UPDATE TO YOUR xPub BIP32 Key	
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
    $displayMsg = "You must enter a name";
  } else if(!isset($_POST['donorNote']) || trim($_POST['donorNote']) ==''){
    $displayMsg = "You must enter a note";
  } else {
  $donorName = $_POST['donorName'];
  $donorNote = $_POST['donorNote'];
  
  $donorName = mysqli_real_escape_string($conn, $donorName);
  $donorNote = mysqli_real_escape_string($conn, $donorNote);
  mysqli_query($conn, "INSERT INTO donate (postid, donor, note) VALUES ('$postid', '$donorName', '$donorNote')");
		//generate address
		$callback_url = $siteRoot."donorcallback.php?postid=".$postid."&secret=".$secret;
		$receive_url = "https://api.blockchain.info/v2/receive?key=".$api_key."&xpub=".$xpub."&callback=".urlencode($callback_url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $receive_url);
		$ccc = curl_exec($ch);
		$json = json_decode($ccc, true);
		$displayMsg = "Please Send Donation to <a href='bitcoin:".$json["address"]."'>".$json["address"]."</a><br>
		<img src='http://chart.googleapis.com/chart?chs=125x125&cht=qr&chl=".$json["address"]."' width='125'>";
		}
}

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
<?php if(isset($displayMsg)){ echo $displayMsg; } ?>
<br>
</html>
