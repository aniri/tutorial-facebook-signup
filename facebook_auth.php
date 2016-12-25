<?php
session_start();
include ("connect.php");

$code = $_GET['code'];

$app_id = "YOUR_APP_ID";
$app_secret = "YOUR_APP_SECRETE";
$my_url = "YOUR_REDIRECT_URL";

if(empty($code)) {
	$_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
	$dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
	. $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
	. $_SESSION['state'];

	echo("<script> top.location.href='" . $dialog_url . "'</script>");
}

if($_REQUEST['state'] == $_SESSION['state']) {
	$token_url = "https://graph.facebook.com/oauth/access_token?"
	. "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
	. "&client_secret=" . $app_secret . "&code=" . $code . "&scope=publish_stream,email";

	$response = @file_get_contents($token_url);
	$params = null;
	parse_str($response, $params);

	$graph_url = "https://graph.facebook.com/me?access_token=" 
	. $params['access_token'];

	$user = json_decode(file_get_contents($graph_url));
	
	$username = $user->username;
	$email = $user->email;
	$facebook_id = $user->id;
	
	// check if user in db => login
	$result = mysql_query("select * from `YOUR_TABLE_NAME` where `facebook_id`='$facebook_id'");
	if (mysql_num_rows($result) == 1)
	{
		$usr = mysql_fetch_array($result);
		$_SESSION['username'] = $usr['username'];
		$_SESSION['uid'] = $usr['id'];
		$_SESSION['access_token'] = $params['access_token'];
		
		?>
		<script>
			top.location.href='home.php'
		</script>
		<?php
	}
	else // if user not in db
	{
		$join_date  = date('Y-m-d h:i:s');
		$query = mysql_query("INSERT INTO `YOUR_TABLE_NAME` (username, email, facebook_id, join_date) 
										VALUES ('$username', '$email', '$facebook_id', '$join_date')");  
		
		$_SESSION['uid'] = mysql_insert_id(); 
		$_SESSION['username'] = $username; 
		$_SESSION['access_token'] = $params['access_token'];
	 
		?>
		<script>
			top.location.href='welcome.php'
		</script>
		<?php
	}
}
else {
	echo("The state does not match. You may be a victim of CSRF.");
}

 ?>
