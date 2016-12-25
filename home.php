<?php
session_start();
include ("connect.php");

$uid = $_SESSION['uid'];

$usr = mysql_query("select * from `YOUR_TABLE_NAME` where id=$uid");
$usr = mysql_fetch_array($usr);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Tutorial Demo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

    <script>
    function updateStatus(url, access_token, app_id){
      document.getElementById('post_button').disabled = true;
      message = document.getElementById('status').value;

      var jqxhr = $.post(url, { "access_token": access_token, "message": message, "app_id": app_id },
         function(data) {
           alert("Status updated successfully!");
           document.getElementById('post_button').disabled = false;
           document.getElementById('status').value = "";
         })
        .success(function() { alert("second success"); })
        .error(function(data, textStatus, jqXHR) { 
          if (data.status == 200){
            alert("Status updated successfully!");
            document.getElementById('post_button').disabled = false;
            document.getElementById('status').value = "";
          }
        })
        .complete(function() {  });
    }
      

    function testDelete()
    {
      return confirm("Are you sure you want to delete your account?");
    }

    </script>
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Tutorial Demo</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li><a href="index.php">Home</a></li>
              <li class="active"><a href="home.php">My account</a></li>
              <li><a href="http://aniri.ro/geek/tutorials/how-to-make-website-registration-easier-using-facebook-accounts">Read tutorial</a></li>
              <li><a href="#">Feedback</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
	
  		<div class="content">

  		  <div class="page-header">
          <h1>signup with facebook</h1>
        </div>

        <div class="row">
          <div class="span8">
            <div class="main">
                <h3><?php echo $usr['username'];?>'s account</h4>


                <?php
                $graph_url = "https://graph.facebook.com/me/friends?access_token=" 
                  . $_SESSION['access_token'];

                  $friends = json_decode(file_get_contents($graph_url));
                  
                  $friends_array = $friends->data;

                  $signedup = 0;

                  $ids_array = Array();

                  for($i = 0; $i < count($friends_array); $i++){
                    $ids_array[] = $friends_array[$i]->{'id'};
                  }

                  $q = mysql_query("select facebook_id from `YOUR_TABLE_NAME`");
                  while ($id = mysql_fetch_array($q)){
                    if (in_array($id['facebook_id'], $ids_array)){
                      $signedup += 1;
                    }
                  }
                ?>

                <div class="alert alert-info" style="margin: 20px;">You have <?php echo count($friends_array);?> friends on facebook, <?php echo $signedup;?> have an account here!</div>

                <div style="margin: 20px;">
                  <form id="fb_form">
                    <textarea rows="7" style="width: 98%" id="status" name="status"></textarea>
                    <br/>
                    <?php 
                    $url = "https://graph.facebook.com/".$usr['facebook_id']."/feed";
                    $app_id = "YOUR_APP_ID";
                    $access_token = $_SESSION['access_token'];
                    ?>
                    <button id="post_button" class="btn btn-primary" onclick="updateStatus('<?php echo $url;?>', '<?php echo $access_token;?>', '<?php echo $app_id;?>')">Post on facebook</button>
                  </form>
                </div>

                <div style="margin: 20px;">
                  <a onclick="return testDelete();" href="deleteAccount.php?id=<?php echo $usr[id];?>"><button class="btn btn-warning">Delete account</button></a>
                  <a href="logout.php"><button class="btn btn-inverse">Logout</button></a>
                </div>

      
            </div>
          </div>
          <div class="span4">
            <h3>Instructions:</h3> 
            <div style="padding: 20px;">
              This is a tutorial to show you how to sign up users to your application using their facebook account. 
              <br/><br/>
              You can read the tutorial <a href="http://aniri.ro/geek/tutorials/how-to-make-website-registration-easier-using-facebook-accounts">here</a>.
              <br/><br/>
              On this page you can view some info about your facebook friends and update your status. Try it out!
            </div>
          </div>
        </div>

  		</div>

  		<?php include "footer.php";?>
    </div> 

  </body>
</html>
