# How to make website registration easier using facebook accounts

:exclamation: old tutorial, needs updates :exclamation:
## What are we going to build?

In this tutorial we will learn how we can facilitate user registration but letting users register to our site using their facebook accounts. This way, they won’t have to fill in any data, just grant the app access to their facebook account and that’s it! Their new account is created and they can easily login to our website.

We will be making calls to facebooks [Graph API](https://developers.facebook.com/docs/graph-api) in order to retrieve the necessary information. In case you are not familiar with it, you might want to take a look at the documentation to get an idea of what it does.

You can view the working demo of what we are going to build here.

The tutorial will have two parts: the first will cover the basic signup/login issues, the second will cover the extra features we will add to our app (viewing friends info and posting on the wall).

Let’s get started!

## Getting started

You would be using this is you have a website  that users can register to and you want to make the registration easier for them by allowing them to register with their facebook accounts. In the tutorial, I will only show you how to use facebook accounts for registration, not how to add this to your existing registration. But it shouldn’t be difficult to integrate if you understand the tutorial. If you have questions on that, just ask.

For this tutorial you will need some basic PHP and Javascript knowledge.

## Creating a new facebook app

The first thing we have to do is create a new facebook app. To do this, [go here](https://developers.facebook.com/apps) and chose to create a new app. You will get to the screen when you have to fill in the app info. The screen will look something like this:

You will have to fill in the following:
- A display name for your app
- Your email address
- The domain where the app will be hosted
- The site url – this is the most important value, it is the url of the page where the app will be redirected after facebook authorization. This page will hold the logic for registering new users and loggin in returning users.

So, make sure you fill in the site url correctly and keep in mind the values at the top of the page (app is and app secret) as we will need them later.

And that’s it! You now have a facebook app. Let’s see how we can use it!

## Creating the db

Next we have to create the db table to hold the data about our users. Let’s suppose we want to store the following data about our users:

- Username
- Email
- Name

You will probably want to store more data in a real application, but these will do for the tutorial.

Apart from these, we will also want to store the facebook id of the users. 

The php code for connecting to the database looks like this:

```
<?php
 $server = "YOUR_SERVER_ADDRESS";
 $username = "YOUR_USERNAME";
 $password = "YOUR_PASSWORD";
 $database = "YOUR_DATABASE_NAME";
 $connId = mysql_connect($server,$username,$password) or die("Cannot connect to server");
 $selectDb = mysql_select_db($database,$connId) or die("Cannot connect to database");
?>
```

## Designing the site

Our demo website will have a few pages:
- index.php – the starting point -the users can choose to signup/login with facebook from here
- welcome.php – the users will be redirected here after creating a new account
- home.php – the main user page, the user will view some info about their facebook friends, have the options to post a new status on facebook and delete their account

I will leave out any design issues. I have chosen to use the [twitter bootstrap](https://getbootstrap.com/2.3.2/) library to make a nice demo, but you can choose whatever you like for yours. The design on the website is not in the scope of this tutorial.

## Signup with facebook

The main page, index.php will hold one button to allow users to signup/login. The code looks like this:

```
<a href="https://www.facebook.com/dialog/oauth?client_id=YOUR_APP_ID&redirect_uri=YOUR_APP_REDIRECT_URL&scope=publish_stream,email" title="Signup with facebook">
 <button>Signup with facebook</button>
</a>
```

All we have to do in order to do this, is redirect the user to the facebook oauth dialog where they have to allow the usage of our app.

All we have to do is make a call to the url https://www.facebook.com/dialog/oauth and mention a few parameters:

- client_id – your app id - remember the value from when we created the facebook app? Here’s one of the places we need it :)
- redirect_uri – the url where you want the app the redirect after authenticating with facebook

these two are the only mandatory values

You can see that I added a new one: scope. A simple app allows you only to access the basic info that users made public from their facebook accounts. If you want to access more info or be able to use their account in other ways, like posting on their wall from you app and such, you will need to be granted special permissions by the user. This is why we use the scope parameter, to specify the permissions we need for our app. I added the publish_stream permission, which will allow us to post on user wall. You can view [the complete list of permission here](https://developers.facebook.com/docs/facebook-login/permissions).

When clicking on the button, the user will get redirected to the authentication screen from facebook and after they grant the permissions, they will be redirected to our app. They will receive a code which we will have to use in order to get an access token for the user. Why do we need an access token? Well, every request for user data that we want to make to facebook will need this access token. So here’s how we get it:

```
$app_id = "YOU_APP_ID";
$app_secret = "YOUR_APP_SECRET";
$my_url = "YOUR_APP_REDIRECT_URL";
$token_url = "https://graph.facebook.com/oauth/access_token?"
 . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
 . "&client_secret=" . $app_secret . "&code=" . $code . "&scope=publish_stream,email";
 
$response = @file_get_contents($token_url);
$params = null;
parse_str($response, $params);
```

We again need to app id and secret. With these and the redirect url (the one we set when creating the app) and the code we received we will make a new request to facebook to get the access token. After executing the code above, the access token will be stored in $params['access_token'].

With this, we can make a request to the facebook graph api and get the user data, like so:

```
$graph_url = "https://graph.facebook.com/me?access_token="
 . $params['access_token'];
 
$user = json_decode(file_get_contents($graph_url));
$username = $user->username;
$email = $user->email;
$facebook_id = $user->id;
```

We will get the data we decided to use for the app: username, facebook_id and email address.

What we have to do next is check whether the user has already registered for our app. We will check if the facebook_id is already in our db.  If it is, then the user already has an account and they will be redirected to their home page (they will be logged into their account). If the user is not in db, then we will add them and redirect them to the welcome page. Like this:

```
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
```

As you can see, we have also set some variables in session. We will need these to check the id/username of the logged in user and the access token. Remember I said we need this for every request we will make to the Facebook Graph API! And that’s it!

For more info, you can see the facebook documentation on authentication [here](https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow).

And that's it for today, the [part 2](https://github.com/aniri/tutorial-facebook-signup/blob/master/tutorial-part2.md) of the tutorial is available here!
