
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
              <li class="active"><a href="index.php">Home</a></li>
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
              <div class="alert alert-info">
                <h4>Hello stranger!</h4>
              </div>

              <a href="https://www.facebook.com/dialog/oauth?client_id=YOUR_APP_ID&redirect_uri=YOUR_REDIRECT_URL&scope=publish_stream,email" title="Signup with facebook">
                <button class="btn btn-primary">Signup with facebook</button>
              </a>
      
            </div>
          </div>
          <div class="span4">
            <h3>Instructions:</h3> 
            <div style="padding: 20px;">
              This is a tutorial to show you how to sign up users to your application using their facebook account. 
              <br/><br/>
              You can read the tutorial <a href="http://aniri.ro/geek/tutorials/how-to-make-website-registration-easier-using-facebook-accounts">here</a>.
              <br/><br/>
              To test it out, press the 'sign up' button.
            </div>
          </div>
        </div>

  		</div>

  		<?php include "footer.php";?>
    </div> 
  </body>
</html>
