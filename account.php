<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<link href="https://fonts.googleapis.com/css?family=Barlow+Semi+Condensed" rel="stylesheet"> 
	<style type='text/css'>
	html{
		overflow:hidden;
	}
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
#yourAccount {
	border:solid 1px black;
	background:white;
	position:absolute;
	left:20px;
	top:100px;
	height:85%;
	width:97%;
	color:black;
}
#yourAccount img{
	padding-left:10px;
	padding-top:10px;
}
#yourAccount #username{
	position:absolute;
	left:30%;
	top:-0px;
	font-family: 'Barlow Semi Condensed', sans-serif;
}
#yourAccount  #password{
	position:absolute;
	top:40px;;
	left:30%;
	font-family: 'Barlow Semi Condensed', sans-serif;
}
#yourAccount #password input{
	display:block;
}
#blocked{
	color:black;
	position:absolute;
	left:50%;
	top:50%;

}
#blocked table{
	border-collapse: collapse;
	border:1px solid black;
	
}
#blocked td{
	width:100px;
}

</style>
<?php
session_start();
$uid = $_SESSION['userID'];
//Connection code to DB

$dbUsername = "dbo723134515";
$dbPassword = "babbleapp";
$dbName = "db723134515";
$serverName = "'db723134515.db.1and1.com";
$connection = mysqli_connect($serverName,$dbUsername,$dbPassword,$dbName) or die("Could not connect to server ".mysqli_connect_error());
?>

<?php 
	//Initialise local username and profilePic storage.
	$myUsername = "";
	$profilePicture="";
	//Query usr table for user with the session stored userID.
	$getUsername = "SELECT * FROM usr WHERE userID='$uid'";
	if($query = mysqli_query($connection, $getUsername)){
		$numRows = mysqli_num_rows($query);
		//Check user with that ID exists
		if($numRows > 0){
			while($user = mysqli_fetch_array($query)){
				$myUsername = $user['username'];
				$profilePicture=$user['profilePicture'];
			}
		}
	}
	//If password change has been submitted
	if(isset($_POST['changePass'])){
		$error = "";
		//Validate password (not empty & at least than 8 characters
		if(empty($_POST["oldPassword"])){
			$error .= "Old password cannot be empty!";
		}
		if(empty($_POST["newPassword"])){
			$error .= "New password cannot be empty";
		}else if(strlen($_POST['newPassword']) < 8){
			$error .= "New password must be at least 8 characters!";
		}
		//If the password matches the criteria.
		if(empty($error)){
			
			$oldPassword=$_POST['oldPassword'];
			$newPassword=$_POST['newPassword'];
			//Protect from SQL injection
			$oldPassword= mysqli_escape_string($connection,$oldPassword);
			$newPassword = mysqli_escape_string($connection,$newPassword);
			//Check password against stored password
			$checkPassword = "SELECT * FROM usr WHERE userID = '$uid'";
			//Query for stored password
			if($checkPassResults = mysqli_query($connection, $checkPassword)){
				$checkPassNumRows = mysqli_num_rows($checkPassResults);
				//Check user exists
				if($checkPassNumRows > 0){
					while($usrRowPassword = mysqli_fetch_assoc($checkPassResults)){
						//Check entered password matches stored
						if($oldPassword == $usrRowPassword['password']){
							//Update user password.
							$updatePasswordQuery = "UPDATE usr SET password='$newPassword' WHERE userID='$uid'";
							mysqli_query($connection,$updatePasswordQuery);
							echo "Password updated successfully";
						}else{
							$error .= "Incorrect password. Please try again.";
							echo $error;
						}
					}
				}
			}else{
				echo "SQL ERROR " . mysqli_error($connection);
			}
		}
	}
	//If user uploading profile picture
	if(isset($_POST['upload'])){
		if(empty($_FILES['pictureData'])){
			echo "Please upload a photo!";
		}else{
			//Add profile picture to database.
			$image = addslashes(file_get_contents($_FILES['pictureData']['tmp_name']));
			$insertQuery = "UPDATE usr SET profilePicture='$image' WHERE userID='$uid'";
			if(mysqli_query($connection, $insertQuery)){

			}else{	
				echo "error in sql syntax ". mysqli_error($connection);
			}
		}
		

	}
	//If user selected to delete account and confirmed his choice.
	if(isset($_POST['deleteAccount'])){
		//Delete usr and assosiated relationships.
		$deleteQuery = "DELETE FROM usr WHERE userID = '$uid'";
		$deleteRelationships = "DELETE FROM relationships WHERE uid1='$uid' OR uid2='$uid'";
		if(mysqli_query($connection,$deleteQuery)){
			$mysqli_query($connection,$deleteRelationships);
			header('Location: login.php');
		}else{
			echo "SQL Error: could not delete account " . mysqli_error($connection);
		}
	}
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
		<div id="yourAccount">
			<?php
				//Show user current profile picture or display default.
				if(isset($profilePicture)){
					echo '<img style = "width:200px; height:200px" src="data:image/jpeg;base64,'.base64_encode( $profilePicture ).'"/>';
				}else{
					echo "<img style='width:200px; height:200px;' src = 'https://www.enjoybenefits.co.uk/wp-content/uploads/2017/09/test1-100x100.png'>";
				}
			?>
			<form action="account.php" method="POST" id="profile" enctype="multipart/form-data">
				<p> Upload image: 100x100 <p>
				<input type="file" name="pictureData">
				<input type="submit" name="upload" value="Upload Picture">
			</form>
			<?php
				echo "<p id = 'username'> Username: " . $myUsername . "</p>";
			?>
			<form action="account.php" method="POST" id="password">
				<label for = "oldPass">Old Password:</label>
				<input type="password" id="oldPass" placeholder="Old Password" name="oldPassword">
				<label for = "newPass">New Password:</label>
				<input type="password" id = "newPass" placeholder="New Password" name="newPassword">
				<input type="submit" value="Change Password" name="changePass">
			</form>
			<form action="account.php" method="POST" id="delete">
				<input type="submit" value="Delete Account" name="deleteAccount" onclick="return confirm('Are you sure you want to delete your account?')">
			</form>
		</div>

<div id = "blocked">
	<p> Blocked Users: </p>
	<?php
		$getBlockedRelationsQuery = "SELECT * FROM relationship WHERE blocked='T' ANd uid1='$uid' OR uid2='$uid'";
		if($blockedUserRelationships = mysqli_query($connection,$getBlockedRelationsQuery)){
			
			$blockedUserRows=mysqli_num_rows($blockedUserRelationships);
			if($blockedUserRows>0){
				while($blockedUserRelationship=mysqli_fetch_assoc($blockedUserRelationships)){
					if($blockedUserRelationship['uid1'] == $uid){
						$friendID=$blockedUserRelationship['uid2'];
					}else if($blockedUserRelationship['uid2']==$uid){
						$friendID=$blockedUserRelationship['uid1'];
					}
					$getFriendUser = "SELECT * FROM usr WHERE userID='$friendID'";
					if($friendUserResult=mysqli_query($connection,$getFriendUser)){
						echo "<table border=1>";
						while($friendUser=mysqli_fetch_assoc($friendUserResult)){
							echo "<form action='account.php' method='post'>
							<input type='hidden' name='rID' value='".$blockedUserRelationship['relationshipID']."'>
							<td>".$friendUser['username']."</td>
							<td> <input type='submit' value='Unblock' name='unblock'></td></form>
						";
						}
						echo "</table>";
					}else{
						echo "could not get user data " . mysqli_error($connection);
					}

				}
			}else{
				echo "<p> You have not blocked anyone!</p>";
			}
		}else{
			echo "could not get user relationships " . mysqli_error($connection);
		}

		if(isset($_POST['unblock'])){
			$rID=$_POST['rID'];
			$unblockQuery="UPDATE relationship SET blocked='F' WHERE relationshipID='$rID'";
			mysqli_query($connection,$unblockQuery);
		}
	?>
</div>
<div id="background">
<img src='res/skyline.jpg'>
</div>
</body>

</html>
