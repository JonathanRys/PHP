<?php
//declare variables and populate arrays


$reqfield = array(
0 => $_POST["fname"],
1 => $_POST["lname"],
2 => $_POST["add1"],
3 => $_POST["city"],
4 => $_POST["zip"],
5 => $_POST["phone"],
6 => $_POST["email"],
7 => $_POST["add2"],
8 => $_POST["state"],
9 => $_POST["service"],
10 => $_POST["details"]
);

$name = array(
0 => "fname",
1 => "lname",
2 => "add1",
3 => "city",
4 => "zip",
5 => "phone",
6 => "email",
7 => "add2",
8 => "state",
9 => "service",
10 => "details"
);

$fieldname = array(
0 => "First Name",
1 => "Last Name",
2 => "Address",
3 => "City",
4 => "ZIP Code",
5 => "Phone number",
6 => "E-mail",
7 => "Address line 2",
8 => "State",
9 => "Service type",
10 => "Details"
);


//********************************************
//**************Declare Functons**************
//********************************************

function died($error) {
//error handler for verifyData 
   	echo "<br />We are very sorry, but there are one or more errors in the form you submitted.<br />";
    echo "These errors are as follows:<br /><br />";
   	echo $error."<br /><br />";
    echo "Please go back and fix these errors.<br /><br />";

	echo '<input type="button" onClick="window.history.go(-1);" value="Back" />';
	return false;
}


function verifyData(){
	
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
	$add1 = $_POST['add1'];
	$add2 = $_POST['add2'];
	$city = $_POST['city'];
	$zip = $_POST['zip'];
	$email = $_POST['email'];
    $telephone = $_POST['phone'];
	$comments = $_POST['details'];
    
	$error_message = "";

    $email_exp = "/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/";
	if(!preg_match($email_exp,$email) == 1){
    	$error_message .= 'Please enter a valid E-mail address.<br />';
	}
	$string_exp = "/^[a-zA-Z .'-]+$/";
	if(!preg_match($string_exp,$fname)){
    	$error_message .= 'The first name you entered does not appear to be valid.<br />';
	}
	$string_exp = "/^[a-zA-Z .'-]+$/";
	if(!preg_match($string_exp,$lname)){
    	$error_message .= 'The last name you entered does not appear to be valid.<br />';
	}
	$string_exp = "/^[a-zA-Z0-9 .'-]+$/";
	if(!preg_match($string_exp,$add1)){
    	$error_message .= 'The address you entered on line 1 does not appear to be valid.<br />';
	}
	if($add2 != "" || NULL){
		if(!preg_match($string_exp,$add2)){
    		$error_message .= 'The address you entered on line 2 does not appear to be valid.<br />';
		}
	}
	$string_exp = "/^[a-zA-Z .'-]+$/";
	if(!preg_match($string_exp,$city)){
    	$error_message .= 'The city you entered does not appear to be valid.<br />';
	}
	$string_exp = "/^[0-9]+$/";
	if(!preg_match($string_exp,$zip)){
    	$error_message .= 'The ZIP Code you entered does not appear to be valid.<br />';
	}
 	$string_exp = "/^[0-9() .-]+$/";
  	if(!preg_match($string_exp,$telephone)){
    	$error_message .= 'The telphone number you entered does not appear to be valid.<br />';
  	}
	if($comments != "" || NULL){
		$string_exp = "/^[a-zA-Z0-9 .,!-\'@&]+$/";
  		if(!preg_match($string_exp,$comments)){
    		$error_message .= 'Please use only alpha-numeric(a-Z, 0-9) characters in your service request.<br />';
  		}
	  	if(strlen($comments) < 4){
    		$error_message .= 'Please add more detail to your service request.<br />';
	  	}
	}
	if(strlen($error_message) > 0){
    	died($error_message);
		return false;
  	}
	return true;
}

function check_input($value){
//Stripslashes
	if (get_magic_quotes_gpc()){
		$value = stripslashes($value);
	}
	//Quote if not a number
	if (!is_numeric($value)){
		$value = "'" . mysql_real_escape_string($value) . "'";
	}
	return $value;
}

function doesExist(){
//return false if the user does not exist
	
	//connect to the host
	$con = mysql_connect("fdb4.runhosting.com", "1205102_bsd", "b9yUHtv9T3");
		
	//check connection
	if (!$con){
		die('<br />Could not connect: ' . mysql_error());
	}

	mysql_select_db("1205102_bsd", $con);//select: "1205102_bsd" database

	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	
	//select enough information to confirm identity
	$sql = "SELECT * FROM customers WHERE lname = '$lname' AND fname = '$fname'";

	$result = mysql_query($sql);

	//check for errors
	if (!mysql_query($sql,$con)){
		die('<br />SELECT Error: ' . mysql_error());
	}

	//check to see if the user exists
	while($row = mysql_fetch_array($result))
	{

		if ($row['phone'] == $_POST['phone'] OR $row['email'] == $_POST['email'])
		{
			mysql_close($con);
			return true;
		}
		else 
		{
			return false;
		}
	}
}
//cleans mail strings
function clean_string($string) {
	$bad = array("content-type","bcc:","to:","cc:","href");
	return str_replace($bad,"",$string);
}

function sendMail($reason){

	//$reasons:
	//0  - no errors
	//1  - file upload error
	//2  - SQL connection error
	
    //E-mail to
    $to = "badcog@gmail.com";

	//$date = new DateTime('2000-01-01');
	
	//E-mail subject
	$subject = "Service request order: " .  date('r');
    
	//format data
    $name = $_POST['fname'] . " " . $_POST['lname'];
	$add = $_POST['add1'];
	if ($_POST['add2'] != "" OR NULL)
	{
		$add .= "\n         " . $_POST['add2'];
	}
	$city = $_POST['city'] . ", " . $_POST['state'] . " " . $_POST['zip'];
    $from = "info@bostonsystemdesign.com";//$_POST['email'];
    $telephone = $_POST['phone'];
    $comments = $_POST['details']; 
    
	//compose E-mail message
	$body = "Form details below.\n\n";
    
    $body .= "Name: ".clean_string($name)."\n";
    $body .= "Address: ".clean_string($add)."\n";
    $body .= "City: ".clean_string($city)."\n";
    $body .= "E-mail: ".clean_string($_POST['email'])."\n";
    $body .= "Telephone: ".clean_string($telephone)."\n";
    $body .= "Details: ".clean_string($comments)."\n";
    

	//create email headers
	$headers = 'From: ' . $from . "\r\n".
	'Reply-To: ' . clean_string($_POST['email']) . "\r\n" .
	'X-Mailer: PHP/' . phpversion();

	//check for exeptions
	if($reason == 1){
		$subject = "Potential file upload attack! " . date('r');
	}
	else if($reason == 2){
		$subject = "SQL connection error: SQL post failed.";
	}
	
	//send E-mail
	if(mail($to, $subject, $body, $headers))
	{
		return true;
	}
	echo "mail() failed...<br />Trying Pear mail package.<br />";
	
	//Pear Mail code
		require_once "Mail.php";
		
		$host = "mail.bostonsystemdesign.com";
		$port = "25";
		
		$host = "ssl://smtp.gmail.com";
		$port = "465";
		$username = "info@bostonsystemdesign.com";
        $password = "JiGiYD9H";

        $headers = array ('From' => $from,
          'To' => $to,
          'Subject' => $subject);
        $smtp = Mail::factory('smtp',
          array ('host' => $host,
		    'port' => $port,
            'auth' => true,
            'username' => $username,
            'password' => $password));

        $mail = $smtp->send($to, $headers, $body);

        if (PEAR::isError($mail)) {
			echo("<br />" . $mail->getMessage() );
			return false;
		} else {
			return true;
		}
	

}

function fileStore(){

	//allow the following filetypes:
	$allowedExtensions = array("txt","log","dmp","csv","htm","html","xml","css","doc","xls","rtf","ppt","pdf","swf","flv","avi","wmv","mov","jpg","jpeg","gif","png");
	
	foreach ($_FILES as $file) {
		if ($_FILES['file']['tmp_name'] > '') {
			if (!in_array(end(explode(".", strtolower($_FILES['file']['name']))), $allowedExtensions)) {
				die('<br />File: ' . $_FILES['file']['name'] . ' is an invalid file type!<br/>' .
				'<a href="javascript:history.go(-1);"> &lt;&lt; Go Back</a>');
			}
		}
	}
	

	//store files in 'uploads' folder
	$uploaddir = './uploads/';
	$uploadfile = $uploaddir . $_POST['lname'] . "_" . basename($_FILES['file']['name']);

	//if the file is successfully stored notify user.
	if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
		echo "<br />File is valid, and was successfully uploaded.";
		return true;
	}
	//otherwise send an email to the webmaster
	else {
		echo "<br />Possible file upload attack!";
		if(sendMail(1)){
			echo "<br />Webmaster notified.";
			die();
		}
		else{
			echo "<br />Please <a href='mailto:info@bostonsystemdesign.com'>notify webmaster!</a>";
			die();
		}
	}
	return false;
}

function SQLpost(){
//post the user's info to the SQL database

	$field = array(
	0 => "",
	1 => $_POST["fname"],
	2 => $_POST["lname"],
	3 => $_POST["add1"],
	4 => $_POST["add2"],
	5 => $_POST["city"],
	6 => $_POST["state"],
	7 => $_POST["zip"],
	8 => $_POST["phone"],
	9 => $_POST["email"],
	10 => $_POST["service"],
	11 => $_POST["details"]
	);

	$name = array(
	0 => "pid",
	1 => "fname",
	2 => "lname",
	3 => "add1",
	4 => "add2",
	5 => "city",
	6 => "state",
	7 => "zip",
	8 => "phone",
	9 => "email",
	10 => "service",
	11 => "details"		
	);

	$fieldname = array(
	0 => "pid",
	1 => "first name",
	2 => "last name",
	3 => "address",
	4 => "address line 2",
	5 => "city",
	6 => "state",
	7 => "ZIP code",
	8 => "phone number",
	9 => "e-mail",
	10 => "service type",
	11 => "details"
	);
	
	//verify that the data the user plugged in does not contain illegal characters
	if(verifyData()){
		
		//if they are a new customer, start a new db entry
		if ($_POST["new"] == 'new'){

			//verify that the user is not already in the database
			if(doesExist()){
				echo "<script>alert('User already exists.');</script>";
				
				echo "<script>window.history.go(-1);</script>";	
				return false;
			}
			else{
				//connect to the host
				$con = mysql_connect("fdb4.runhosting.com", "1205102_bsd", "b9yUHtv9T3");

				//check connection
				if (!$con){
					echo '<br />Could not connect: ' . mysql_error();
					return false;
				}

				mysql_select_db("1205102_bsd", $con);//select: "1205102_bsd" database

				for($i=1; $i <= 11; $i++){

					$field[$i] = check_input($field[$i]);
					
					if($i==1){
						$fieldList = $name[$i];
						$fieldValue = $field[$i];
					}
					else{
						if($field[$i] != NULL || ""){
							$fieldList .= ", " . $name[$i];
							$fieldValue .= ", " .  $field[$i];;
						}
					}
				}


				//insert data into the table named "customers"
				$sql="INSERT INTO customers ($fieldList) VALUES ($fieldValue)";

				//query and check for errors
				if (!mysql_query($sql)){
					echo '<br />INSERT Error: ' . mysql_error();
					return false;
				}
		
				//execute confirmation code
				echo "Your work request has been sent.  Thank you for choosing Boston System Design";
				mysql_close($con);
				return true;
			}

		}

		//if they are a returning customer update only the missing data.
		else if($_POST["new"] == 'return'){

			if(doesExist()){
				//connect to the host
				$con = mysql_connect("fdb4.runhosting.com", "1205102_bsd", "b9yUHtv9T3");
		
				//check connection
				if (!$con){
					echo '<br />Could not connect: ' . mysql_error();
					return false;
				}

				mysql_select_db("1205102_bsd", $con);//select: "1205102_bsd" database

				//clean data
				for($i=1; $i <= 11; $i++){
					if($field[$i] != "" || NULL){
						$field[$i] = check_input($field[$i]);
					}
				}
				while(strlen($field[7]) <= 5){
					$field[7] = '0' . $field[7];	
				}

				$fname = $field[1];
				$lname = $field[2];
				//select enough information to confirm identity
				$sql = "SELECT * FROM customers WHERE lname = $lname AND fname = $fname";

				$result = mysql_query($sql);

				//check for errors
				if (!mysql_query($sql,$con)){
					echo'<br />SELECT Error: ' . mysql_error();
					return false;
				}

				while($row = mysql_fetch_array($result)){

					if ($row['phone'] == $_POST['phone'] OR $row['email'] == $_POST['email']){
						echo "Updating " . $row['lname'] . ", " . $row['fname'] . "...<br />";

						$pid = $row['pid'];
						//match each entry
						for ($i=1; $i<=11; $i++){
							if($row[$i] != "" || NULL){
								//if the entries don't match
								if ("'" . $row[$i] . "'" != $field[$i]){
	
									//update that field
									$column = $name[$i];
									$value = $field[$i];

									$sql="UPDATE customers SET $column = '$value' WHERE pid='$pid'";

									//check for errors
									if (!mysql_query($sql,$con)){
										echo '<br />UPDATE Error: ' . mysql_error();
										return false;
									}
									echo "<br />The following field has been updated: " . $fieldname[$i] . " ";
								}
							}
						}
						//use the pid to prevent attecks
						$sql="UPDATE customers SET date=CURRENT_TIMESTAMP WHERE pid=$pid";

						//check for errors
						if (!mysql_query($sql,$con)){
							echo '<br />Update Error: ' . mysql_error();
							return false;
						}

						echo "<br />Customer #00" . $row['pid'] . " updated. <br />Thank you for using Boston System Design";
						return true;
					}
				}
				mysql_close($con);
			}
			else{
				echo "<br />User does not exist.";
				return false;
			}
		}
		else{
			echo '<br />Value for "new" undefined';
			return false;
		}
	}
	else{
		echo "<br />Verify failed.";
		return false;
	}
	echo '<br />SQL post fell through';
	return false;
}
//********************************************
//**************** BEGIN CODE ****************
//********************************************

//turn off PHP error reporting
error_reporting(0);


//post to the database
if(SQLpost()){
		
	//send an e-mail
	if(sendMail(0)){
		echo "<br />E-Mail sent.";
	}
	else {
		echo "<br />E-Mail failed.";
	}

	//check for uploaded files
	if ($_FILES['file']['size'] > 0){
		//if files are included, store them
		if(fileStore()){
			echo "<br />Files uploaded.";
		}
		else{
			echo "<br />File upload failed, please try again.";
		}
	}
}
else {
	echo "<br />SQL post failed.";

	if(sendMail(2)){
		echo "<br />Error report sent.";
	}
	else {
		echo "<br />Error report failed.";
	}
}


echo '<br /><br />';
echo '<form action="index.html">';
echo '<input type="button" onClick="window.history.go(-1);" value="Back" />&nbsp;';
echo '&nbsp;<button type="submit">Home</button>';
echo '</form>';


?>