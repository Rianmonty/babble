<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<link href="https://fonts.googleapis.com/css?family=Barlow+Semi+Condensed" rel="stylesheet"> 
	<style type='text/css'>
	body {
		min-width:900px;
		padding:0px;
		margin:0px;
		color:black;
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
#navbar img{
		width:190px;
		height: 70px;
		
	}
#navbar a:hover{
	background-color:#6bcbff;
}
	</style>
<?php
session_start();
$uid = $_SESSION['userID'];
//Connection code to DB
$dbUsername = "rian";
$dbPassword = "BabbleApp1";
$dbName = "babbledb";
$serverName = "babblechat";
$connection = mysqli_connect($serverName,$dbUsername,$dbPassword,$dbName);
?>
</head>

<body>
	<div id="navbar">
		<img src = "res/logo.jpg">
		<?php
		if(!empty($uid)){
			echo '
				<a href="logout.php" id="account">Logout</a>
				<a href="account.php" id="account">Your Account</a>
				<a href="mainPage.php" id="navChat">Chat</a>
			';
		}else{
			echo '
			<a href="mainPage.php" id="chat">Chat</a>
			<a href="register.php" id="register">Register</a>
			<a href="login.php" id="login">Login</a>
			<a href="account.php" id="account">Your Account</a>
			';
		}
	
	?>
		
	</div>
	<div id='addFriendDiv'>
		<form name="friend" action='addFriend.php' method='post')>
			<label for="friendNameInput">Friend Name</label>
			<input value = "" type='text' placeholder='Friend Name' name="username" id="friendNameInput">
			<input type='submit' value='Find Friend' name="submit">
		</form>
	</div>
	<?php
	
	$error = "";
	
	//Input validation, not empty and more than 4 characters
	if(isset($_POST['submit'])){
		
		if(empty($_POST['username'])){
			
			$error .= "Search username cannot be empty";
		}else{
			if(strlen($_POST['username']) < 4){
				$error .= "Username must be more than 4 characters!";
			}
		}
		if(empty($error)){

		
	
	//Get friends with username.
	$username = $_POST['username'];
	$getFriends = "SELECT * FROM usr WHERE username='$username';";
	//Declare the variables we are getting from the usr database
	$friendID = "";
	$friendUsername = "";
	$friendProfilePicture="";
	$friendLastOnline = "";
	
	if($getFriendResult=mysqli_query($connection, $getFriends)){
		$numRows = mysqli_num_rows($getFriendResult);
		
		// Check user with that username exists
		if($numRows > 0){
			while($friend = mysqli_fetch_assoc($getFriendResult)){
				//Set variables
				$friendUsername=$friend['username'];
				$friendID = $friend['userID'];
				$friendProfile=$friend['profilePicture'];
				$friendLastOnline=$friend['lastOnline'];
				$myID=$_SESSION['userID'];
				//Create cell to display data and add friend.
				$addButton = '<input type="submit" value="add friend" name="addFriend">';
				$getUserInvolvedRelationships = "SELECT * FROM relationship WHERE uid1='$myID' OR uid2='$myID'";
				if($userInvolvedRelationshipResults = mysqli_query($connection,$getUserInvolvedRelationships)){
					$userInvolvedRows = mysqli_num_rows($userInvolvedRelationshipResults);
					//Check there is relationships the user is involved in
					if($userInvolvedRows > 0){
						while($involvement = mysqli_fetch_assoc($userInvolvedRelationshipResults)){
							//If the user is uid1:
							if($myID == $involvement['uid1']){
								$getRelationshipExists = "SELECT * FROM relationship WHERE uid1='$myID' AND uid2='$friendID'";
								if($relationshipExistResult = mysqli_query($connection,$getRelationshipExists)){
									$existRows=mysqli_num_rows($relationshipExistResult);
									if($existRows>0){
										$addButton = '<input type="submit" value="Already Friends" name = "addFriend" disabled="true">';
										
									}
								}
								
							}else if($myID == $involvement['uid2']){
								$getRelationshipExists = "SELECT * FROM relationship WHERE uid1='$friendID' AND uid2='$myID'";
								if($relationshipExistResult = mysqli_query($connection,$getRelationshipExists)){
								$existRows=mysqli_num_rows($relationshipExistResult);
								if($existRows>0){
									$addButton = '<input type="submit" value="Already Friends" name = "addFriend" disabled="true">';
										
								}
							}

						}
					}
				}
			}
				
				echo '<div id = "friendListItem">
						<form action = "addFriend.php" method="POST">
							<p>'.$friendUsername.'</p>
							<p>'.$friendLastOnline.'
							<input type="hidden" value="'.$friendID.'" name="friendID">
							'.$addButton.'
							<input type="submit" value="Block User" name="block">
						</form>
					</div>';
			}
			
			//Get userID and friendID.
			$myID = $_SESSION['userID'];
			
			
		}else{
			echo "<p> Friend with that username does not exist!</p>";
			
		}
	
	}else{
		echo "Could not execute query" . mysqli_error($connection);
	}
	
	}else{
		echo "<p style='color:red;'>" . $error . "</p>";
	}
}

//If user clicked the add friend button
if(isset($_POST['addFriend'])){
	$myID=$_SESSION['userID'];
	$friendID = $_POST['friendID'];
	$insertRelationship = "INSERT INTO relationship (uid1,uid2,blocked) VALUES ('$myID','$friendID','F')";
	mysqli_query($connection,$insertRelationship);
}else{
	
}
if(isset($_POST['block'])){
	$friendID=$_POST['friendID'];
	$myID = $uid;
	$getUserInvolvedRelationships = "SELECT * FROM relationship WHERE uid1='$myID' OR uid2='$myID'";
	if($userInvolvedRelationshipResults = mysqli_query($connection,$getUserInvolvedRelationships)){
		$userInvolvedRows = mysqli_num_rows($userInvolvedRelationshipResults);
		//Check there is relationships the user is involved in
		if($userInvolvedRows > 0){
			while($involvement = mysqli_fetch_assoc($userInvolvedRelationshipResults)){
				//If the user is uid1:
				if($myID == $involvement['uid1']){
					$getRelationshipIfExists="SELECT * FROM relationship WHERE uid1='$myID' AND uid2='$friendID'";
					if($getRelationshipIfExistResults=mysqli_query($connection,$getRelationshipIfExists)){
						$numRow=mysqli_num_rows($getRelationshipIfExistResults);
						if($numRow>0){
							while($relationship=mysqli_fetch_assoc($getRelationshipIfExistResults)){
								$relationshipID=$relationship['relationshipID'];
								$updateRelationship = "UPDATE relationship SET blocked = 'T' WHERE relationshipID='$relationshipID'";
								mysqli_query($connection,$updateRelationship);	
							}
						}else{
							$insertRelationship = "INSERT INTO relationship (uid1,uid2,blocked) VALUES ('$myID','$friendID','T'";
						}
					}
				}else if ($myID == $involvement['uid2']){
					$getRelationshipIfExists="SELECT * FROM relationship WHERE uid1='$friendID' AND uid2='$myID'";
					if($getRelationshipIfExistResults=mysqli_query($connection,$getRelationshipIfExists)){
						$numRow=mysqli_num_rows($getRelationshipIfExistResults);
						if($numRow>0){
							while($relationship=mysqli_fetch_assoc($getRelationshipIfExistResults)){
								$relationshipID=$relationship['relationshipID'];
								$updateRelationship = "UPDATE relationship SET blocked = 'T' WHERE relationshipID='$relationshipID'";
								mysqli_query($connection,$updateRelationship);	
							}
						}else{
							$insertRelationship = "INSERT INTO relationship (uid1,uid2,blocked) VALUES ('$myID','$friendID','T'";
						}
					}
				}
			}
		}
	}
	
}
?>
</body>

</html>
