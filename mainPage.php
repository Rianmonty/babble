<html>
<head>
	
	<?php
		session_start();
		
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	 <script  type="text/javascript">
            window.setInterval("refreshDiv()", 2000);
            function refreshDiv(){
               // document.getElementById("messages").innerHTML = "Testing " + counter;
            	$("#messages").load(window.location.href + " #messages>*");
				
            }
      </script>

<style type="text/css">
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
	top:-10px;
	width:100%;
	height:100%;
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

#addFriendDiv{
	background-color:#40aae2;
}

#chats{

	position:absolute;
	top:70px;
	left:-1px;
	
	overflow-y:auto;
	width:255px;
	overflow-x:hidden;

	height:85%;
}
#chatCell{
	padding-top:10px;
	border-style:solid;
	border-color:black;
	background-color:white;
	height:200px;
	color:black;
	width:250px;
}
#chatCell input[type=submit]{
	background:none;
	border:none;
	font-family:'Barlow Semi Condensed', sans-serif;	
	font-size:25px;
	cursor:pointer;
}
#addFriendDiv{
	position:absolute;
	bottom:0px;
	height:100px;
	display:inline;

}
#addFriendDiv form{
	padding-top:20px;
	padding-left:10px;
	padding-right:10px;
	color:white;
	
}
#addFriendDiv input[type=text]{
	background-color:#40aae2;
	color:white;
	font-family: 'Barlow Semi Condensed', sans-serif;
	border:none;
	border-bottom:2px solid white;
}
#addFriendDiv label{
	
	color:black;
	font-family: 'Barlow Semi Condensed', sans-serif;
	display:block;
	font-size:17px;

}
#sendMessage{
	display:inline;
	width:83%;
	position:absolute;
	bottom:0px;
	left:250px;
	height:100px;
	
	background-color:white;
}
#sendMessage form{
	padding-left:30px;
	padding-top:30px;
}
#sendMessage input[type=text]{
	width:60%;
	height:40px;
	font-family: 'Barlow Semi Condensed', sans-serif;
	font-size:20px;
}
#messages{
	position:absolute;
	top:70px;
	left:254px;
	width:82.3%;
	height:610px;
	overflow-y:auto;
	overflow-x:hidden;
}
#messageCell{

	bottom:175px;
	width:98%;
	padding-left:10px;
	background-color:grey;
	height:50px;

	border:solid 1px black;
}
#messageCell p  {
	font-family: 'Barlow Semi Condensed', sans-serif;
	display:inline;
}
#messageCell #name{
	text-decoration:bold;
	font-size:20px;
}
#messageCell #date{
	padding-top:0px;
	float:right;
	position:relative;
	right:2%;
}
#messageCell #message{
	font-size:30px;
	
}
p{
	font-family: 'Barlow Semi Condensed', sans-serif;
}
html{
	overflow:hidden;
}

</style>
<link href="https://fonts.googleapis.com/css?family=Barlow+Semi+Condensed" rel="stylesheet"> 
<title> Chat App </title>

<?php
	ob_start();
	//Set uid variable to the logged in users ID.
	$uid = $_SESSION['userID'];
	//Set server details.
	$dbUsername = "dbo723134515";
	$dbPassword = "babbleapp";
	$dbName = "db723134515";
	$serverName = "'db723134515.db.1and1.com";
	//Connect to server
	
	$connection = mysqli_connect($serverName, $dbUsername, $dbPassword, $dbName);
	if($connection){

	}else{
		//If server connection could not be established die and display error
		die (mysqli_error);
	}
	//Get the current users username
	$myUsername = "";
	$getUsername = "SELECT * FROM usr WHERE userID='$uid'";
	if($query = mysqli_query($connection, $getUsername)){
		$numRows = mysqli_num_rows($query);
		//Check user with that ID exists
		if($numRows > 0){
			while($user = mysqli_fetch_array($query)){
				$myUsername = $user['username'];
			}
		}
	}
//	echo $username;
	
	

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
			
			<a href="register.php" id="register">Register</a>
			<a href="login.php" id="login">Login</a>
			<a href="account.php" id="account">Your Account</a>
			';
		}
	
	?>
	<div id="chats">
	<?php
		//Get all the friendships the user has.
		$getRelationships = "SELECT * FROM relationship WHERE uid1 = '$uid' OR uid2 = '$uid'";
	
		if($relationshipResults=mysqli_query($connection, $getRelationships)){
			$numRows=mysqli_num_rows($relationshipResults);
			//Check friendships exist with that user
			if($numRows > 0){
					
					while($relationship=mysqli_fetch_assoc($relationshipResults)){
						$rID=$relationship['relationshipID'];
						//secho "test";
						//Set the friendID to the other ID in the relationship table
						if($uid==$relationship['uid1']){
							
							$friendID = $relationship['uid2'];
						}else if($uid==$relationship['uid2']){
							
							$friendID = $relationship['uid1'];
						}

						if($relationship['blocked'] == 'F'){
						//Initialise friend detail variables
						$friendName="";
						$friendProfile="";
						$lastOnline="";
						//Query to fetch friend details from usr table
						$getFriendDetails="SELECT * FROM usr WHERE userID='$friendID'";
						if($friendDetailResults=mysqli_query($connection,$getFriendDetails)){
							$numRowFriend=mysqli_num_rows($friendDetailResults);
							
							//Friend should exist but just to be sure check friend exists
							if($numRowFriend>0){
								//Set details
								while($friendDetails=mysqli_fetch_assoc($friendDetailResults)){
									$friendName=$friendDetails['username'];
									$friendPicture=$friendDetails['profilePicture'];
									$lastOnline=$friendDetails['lastOnline'];
									$imageCode = "";
									if(isset($friendPicture)){
										$imageCode = '<img style = "width:50px; height:50px" src="data:image/jpeg;base64,'.base64_encode( $friendPicture ).'"/>';
									}else{
										$imageCode =  "<img style='width:50px; height:50px;' src = 'https://www.enjoybenefits.co.uk/wp-content/uploads/2017/09/test1-100x100.png'>";
		
									}
									//Get most recent message for that username.
									$mostRecentMessage = "SELECT message, userID, readStatus FROM message WHERE relationshipID='$rID' ORDER BY dateTime DESC";
									$recentMessage="";
									
									if($recentMessageResult = mysqli_query($connection, $mostRecentMessage)){
										
										$numRowMessage = mysqli_num_rows($recentMessageResult);
										if($numRowMessage > 0){
											$recentMessageRows =mysqli_fetch_row($recentMessageResult);
											$sendeeID = $recentMessageRows[1];
											$readResult=$recentMessageRows[2];
											$getMostRecentSendee = "SELECT username FROM usr WHERE userID='$sendeeID'";
											$sendeeUsrname = "";
											if($sendeeResult = mysqli_query($connection,$getMostRecentSendee)){
												$sendeeRow=mysqli_fetch_row($sendeeResult);
												$sendeeUsrname=$sendeeRow[0];
											}
											$recentMessage=$sendeeUsrname.": ".$recentMessageRows[0];
										
										}
									}
								else{
											
										}
										
									
									//Echo a cell for each relationship the user has with the 
									//username, last online and most recent message with that
									//user.
									
									echo '<div id="chatCell">
										<form action="mainPage.php" method="GET">
											<input name="relationship" type="hidden" value="'.$rID.'">
										'.$imageCode.'
											
											
											<input name="friendID" type = "hidden" value="'.$friendID.'">
											<input name="openChat" type="submit" value="'.$friendName.'">
											<p> '.$recentMessage.'</p>
											<p>Last Online:'.$lastOnline.'</p>
											<p>Read:'.$readResult.'</p>
										</form>
									</div>';
								}
							}else{
								//echo "Something went very very wrong" . mysqli_error($connection);
							}
						}else{
							echo "Could not find friend " . mysqli_error($connection);
						}

						
					}
				}
			}else{
				echo "you have no friends, why not add some?";
			}
		}else{

			echo "could not read relationships" . mysqli_error($connection);
		}
	?>
	</div>
	<div id='messages'>
	<?php
		$relationshipID = 0;
		if(!isset($_SESSION['currentRelationship'])){
			$_SESSION['currentRelationship'] = 0;
		}
		if(!isset($_SESSION['friendName'])){
			$_SESSION['friendName']=0;
		}
		if(!isset($_SESSION['friendID'])){
			$_SESSION['friendID'] = 0;
		}
		if(isset($_GET['openChat'])){
			
			//Check user is involved in conversation
			$relationshipID = $_GET['relationship'];
			$_SESSION['currentRelationship'] = $relationshipID;
			$involved = FALSE;
			$getInvolvementQuery = "SELECT * FROM relationship WHERE relationshipID='$relationshipID'";
			if($getInvolvementResult=mysqli_query($connection,$getInvolvementQuery)){
				while($involvement=mysqli_fetch_assoc($getInvolvementResult)){
					if($uid==$involvement['uid1'] || $uid==$involvement['uid2']){
						$involved = TRUE;
					}else{
						$involved = FALSE;
					}
				}
			}
			
			
			if($involved){
			$friendID = $_GET['friendID'];
			$friendName="";
			//Update read status
		
			
			//Get the name assosiated with the ID
			$getFriendNameQuery = "SELECT username FROM usr WHERE userID='$friendID'";
			if($getFriendNameResult=mysqli_query($connection,$getFriendNameQuery)){
				while($friend=mysqli_fetch_assoc($getFriendNameResult)){
					$friendName=$friend['username'];
				}
			}
			$_SESSION['friendName'] = $friendName;
			$_SESSION['friendID'] = $friendID;
			//Get messages assosiated with the relationship in descending order.
			$getMessages = "SELECT * FROM message WHERE relationshipID = '$relationshipID' ORDER BY dateTime ASC";
			//Query
			if($messageResults = mysqli_query($connection,$getMessages)){
				//Check there is messages
				$numRows = mysqli_num_rows($messageResults);
				if($numRows > 0){
					//If there is more than 0 messages then output them all.
					//Work out infinite scroll later and limit messages displayed.
					while($messageRow = mysqli_fetch_assoc($messageResults)){
						$message=$messageRow['message'];
						$dateTime = $messageRow['dateTime'];
						$senderID=$messageRow['userID'];
						if($friendID==$senderID){
							
						$updateReadQuery = "UPDATE message SET readStatus='T' WHERE relationshipID='$relationshipID'";
						if(mysqli_query($connection,$updateReadQuery)){
							header('Location: login.php');
						}else{
							echo mysqli_error($connection);						
						}
				
						echo '<div id="messageCell">
							<p id = "name">'.$friendName.'</p>
							<p id = "message">'.$message.'</p>
							<p id = "date">'.$dateTime.'</p>
						</div>';
							
						}  if($uid==$senderID){
							
							
							echo '<div id="messageCell">
							<p id = "name">'.$myUsername.'</p>
							<p id = "message">'.$message.'</p>
							<p id = "date">'.$dateTime.'</p>
							</div>';
							
						}

					}
				}else{
					echo '<p> You have no messages with '.$friendName.' why not start a conversation?</p>';
				}
			}else{
				echo "Could not get messages" . mysqli_error($connection);
			}
		}
		}
	?></div>
	</div>
	<div id="sendMessage">
		<form action="mainPage.php" method="post">
			<input autocomplete='off' name="message" type="text">
			<input type="submit" value="Send" name="sendMessage">
		</form>
	</div>
	<?php
		// if message sent execute code
		if(isset($_POST['sendMessage'])){
			$messageError="";
			//Validate message not empty
			if(empty($_POST['message'])){
				$messageError.="Message cannot be empty";
			}else{
				if(strlen($_POST['message']) < 1){
					$messageError.="Message cannot be empty";
				}
			}
			$relationshipID=$_SESSION['currentRelationship'];
			if(empty($messageError)){
				//If the relationship ID is set
				if(isset($relationshipID)){
					//Add message to database
					$message=$_POST['message'];
					$sendMessageQuery = "INSERT INTO message(relationshipID, message, userID, dateTime,readStatus) VALUES ('$relationshipID', '$message', '$uid', NOW(),'F')"; 
					if(mysqli_query($connection, $sendMessageQuery)){

					}else{
						echo 'Could not send message' . mysqli_error($connection);
					}
				}else{
					echo "Relationship ID not set!";
				}
			}
			//Redirect to the same chat to refresh page and show message
			header('Location:mainPage.php?relationship='.$_SESSION['currentRelationship'].'&friendID='.$_SESSION['friendID'].'&openChat='.$_SESSION['friendName']);
		}
	?>
	<div id='addFriendDiv'>
		<form name="friend" action='addFriend.php' method='post'>
			<label for="friendNameInput">Friend Name</label><input type='text' placeholder='Friend Name' name="username" id="friendNameInput">
			<input type='submit' value='Find Friend' name="submit">
		</form>
	</div>
	
</div>

<div id="background">
<img src='res/skyline.jpg'> 
</div>

</body>


</html>
