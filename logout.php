<html>
	<header>
		<?php
			session_start();
			//Unset all session variables
			session_unset();
			//Destroy session
			session_destroy();
			//Redirect to login.
			header('Location:login.php');
		?>
	</header>
</html>