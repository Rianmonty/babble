<html>
<head>
	<?
		//If user is already logged in redirect to main page.
		if(isset($_SESSION['userID'])){
			header('Location: mainPage.php');
		}
	?>
<style type="text/css">
body {
	min-width:900px;
	padding:0px;
	margin:0px;
  }
#navbar{

background-color: #40aae2 ;
width:100%;
height:70px;


color:white;

}
#navbar h1{
float:left;
color:white;
position:relative;
font-family: 'Barlow Semi Condensed', sans-serif;
bottom:10px;
left: 10px;
}
#navbar a{
text-decoration:none;
color:white;
font-family: 'Barlow Semi Condensed', sans-serif;
font-size:30px;
padding-top:10px;
overflow:hidden;
float:right;
height: 60px;
padding-left:10px;
text-align:center;

}
#navbar a:hover{
background-color:#6bcbff;
}
#navbar img{
	width:190px;
	height: 70px;
	
}

#background{
position: absolute;
z-index: -10;
width:100%;
height:100%;
top:-1px;
overflow-x:hidden;
overflow-y:hidden;
}
#background img{
position:absolute;
left: 0px;
top:0px;
height:100%;
width:100%;
overflow-x:hidden;
overflow-y:hidden;	
}
#loginbox{

background-color:#40aae2;
border-radius:4px;
width:300px;
border:black;
border-style:solid;
position: absolute;
left:40%;
top:30%;

}

#loginbox form{

align-content: center;

padding-bottom:10px;
padding-top:30px;
padding-left:50px;

}
#loginbox input[type=text], input[type=password]{
border:none;
border-bottom:2px solid white;

padding-left:10px;	
height:35px;
font-family: 'Barlow Semi Condensed', sans-serif;
font-size:20px;
width:200px;

}
#loginbox input[type=submit]{
width:100px;

height:40px;
position:relative;
left:40px;
border-radius:1px;
border-style:solid;
font-size:20px;
font-family:'Barlow Semi Condensed', sans-serif;
}
#loginbox p{
font-family: 'Barlow Semi Condensed', sans-serif;
}
#loginbox label{

color:white;
font-family: 'Barlow Semi Condensed', sans-serif;
position:relative;

font-size:20px;

}
#loginbox #registerprompt{
	position:relative;
	left:20px;
	font-size:12px;
}
</style>
<link href="https://fonts.googleapis.com/css?family=Barlow+Semi+Condensed" rel="stylesheet"> 
<title> Chat App </title>

<?php
	session_start();
	//Set details for connection
	$dbUsername = "rian@babblechat-mysqldbserver";
	$dbPassword = "BabbleApp1";
	$dbName = "babbledb";
	$serverName = "babblechat-mysqldbserver.mysql.database.azure.com";
	$connection = mysqli_connect($serverName, $dbUsername, $dbPassword, $dbName);
	if($connection){

	}else{
		//If server connection could not be established die and display 
		header('Location: google.com/'.mysqli_error);
		
	}
	//Initialise the error variables used for validation
	$usernameError = "";
	$passwordError = "";
	$error = "";
	//When user submits data
	$gotData = FALSE;
	$accountExists = FALSE;
	if(isset($_POST['submit'])){
		$gotData = TRUE;
		//Check username empty
		if(!empty($_POST['username'])){
			//Check username is over 4 characters
			if(strlen($_POST['username'])< 4){
				$usernameError.="Username must be at least 4 characters!";
			}
		}else{
			$usernameError .= "Username field must not be empty!";
		}
		//Check password empty
		if(!empty($_POST['password'])){
			//Check password is over 8 characters
			if(strlen($_POST['password']) < 8){
				$passwordError .= " Password must be at least 8 characters!";
			}
		}else{
			$passwordError .= " Password cannot be left blank!";
		}
		//Add all the errors into the error variable
		$error = $usernameError . $passwordError;
	
	//Account exists in database, used to check password later
	
	//Set variables to the user input
	$username = $_POST['username'];
	$password = $_POST['password'];
	//echo $accountExists; - debugging 
	
	if(empty($error)){
		//Strip SQL characters that can be used to abuse vulnerabilites
		$username = mysqli_escape_string($connection,$username);
		$password = mysqli_escape_string($connection,$password);
		//Query to check user exists
		$checkExists = "SELECT * FROM usr WHERE username = '$username'";
		//Execute query
		if($resultExist = mysqli_query($connection,$checkExists)){
			$rows = mysqli_num_rows($resultExist);
			//If user does not exist set accountExists to false
			if($rows == 0){
			
				$accountExists=FALSE;
			}else{
				//If user exists set accountExists to true
				$accountExists = TRUE;
				
			}
		}else{
			echo mysqli_error($connection);
		}

		if(!$accountExists){
			//Query to create account
			$passwordCheck= "INSERT INTO usr (username,password) VALUES ('$username', '$password');";
			//Create user and redirect to login screen
			if(mysqli_query($connection,$passwordCheck)){
				header ('Location: login.php');
				echo "logged in";
			}else{
				echo $passwordCheck;
			}
			//Log user in and redirect.
			
			//
			
		}
			
		
		}
	}
	

?>
</head>

<body>
<div id="navbar">
	<img src = "res/logo.jpg">
	<a href="register.php" id="register">Register</a>
	<a href="login.php" id="login">Login</a>
	
	<a href="account.php" id="account">Your Account</a>
	
</div>

<div id="background">
<img src='res/skyline.jpg'> 
</div>

<div id="loginbox">
	<?php
		//If there were any errors in the validation display an error message to the user.
		if(!empty($error)){
			echo '<p style="color:red;">'.$error.'</p>';
		}
		//echo $accountExists;
		//If the account already exists alert user.
		if($gotData){
			if($accountExists){
				echo '<p style="color:red;"> Account already exists</p>';
			}
		}
	?>
	<form method ='POST' action = 'register.php'>
		<label for="username">Username</label><input id= "username" type="text" placeholder="Username" name="username">
		<label for ="password">Password</label><input id="password" type="password" placeholder="Password" name="password">
		<p id = "registerprompt">Not got an account? login <a href="login.php">here</a></p>
		<input type = "submit" name="submit" value="Register">

	</form>
</div>
</body>


</html>
