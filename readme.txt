###############################
#Bitcoin pay-per-post system  #
#Uses blockchain.info API     #
###############################

Remember those good old days of Geocities websites and guestbooks?
Signing guestbooks gave users a feel that they were part of the site. 
This is a new adaptation to guestbooks using bitcoin, posts will only show if the 
user pays/donates to the address. 

You will need a blockchain.info API Key https://api.blockchain.info/v2/apikey/request/

1. Open the setup.php file and enter in your database info (name, username and password)
2. Save the changes to setup.php and run the file on your server to create the table.
3. Open donorbook.php and donorcallback.php and update to your site root, BTC address, database info and create a secret.
4. The $secret can be anything you want it to be.
5. Make sure your donorcallback.php file is placed in the same directory/location as specified in the 'Site Root' for donorbook.php
6. Send the users to donorbook.php using a link or embed it in your site with an iframe

That's it!
