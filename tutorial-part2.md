# How to make website registration easier using facebook accounts part 2

:exclamation: old tutorial, needs updates :exclamation:

## Extra features

Once logged into their accounts, the users can see some info about their friends – the total number of facebook friends they have and how many of their friends are using our app.

Also they have the option to post on their facebook wall.

## Get the friends list

For getting the users friends list, we will have to make another call to the Graph API. The code looks like this:

```
<?php
 
$graph_url = "https://graph.facebook.com/me/friends?access_token=" . $_SESSION['access_token'];
 
$friends = json_decode(file_get_contents($graph_url));
 
$friends_array = $friends->data;
 
$signedup = 0;
 
$ids_array = Array();
 
for($i = 0; $i < count($friends_array); $i++){
 
$ids_array[] = $friends_array[$i]->{'id'};
 
}
 
$q = mysql_query("select facebook_id from `facebook-signup`");
 
while ($id = mysql_fetch_array($q)){
 
if (in_array($id['facebook_id'], $ids_array)){
 
$signedup += 1;
 
}
 
}
 
?>
 
<div style="margin: 20px;">You have <?php echo count($friends_array);?> friends on facebook, <?php echo $signedup;?> have an account here!</div>

```

The url we make a request to is: https://graph.facebook.com/me/friends. And all we have to send as parameter is the access token we have stored in session. The API will return a JSON encoded list of friends usernames and facebook ids. We have printed the total number of friends. For checking how many friends have accounts at our app, we make a query to the db to get all the facebook ids and for each of these we checked if they are in the users friends list. As you can see, we have printed out the result of the count.

## Posting on wall

To post on a users wall we have to make a post request to the url to call the graph api https://graph.facebook.com/THE_FACEBOOK_ID_OF_THE_USER/feed. We will need the following parameters for the call:

- our app id
- the access token
- the message to post on the wall

We have created a simple form on the page with a textarea for the status and a button for posting it:

```
<form id="fb_form">
 
<textarea rows="7" style="width: 98%" id="status" name="status"></textarea>
 
<br/>
 
<?php
 
$uid = $_SESSION['uid'];
         $usr = mysql_query("select * from `YOUR_TABLE_NAME` where id=$uid");
         $usr = mysql_fetch_array($usr);
$url = "https://graph.facebook.com/".$usr['facebook_id']."/feed";
 
$app_id = "YOUR_APP_ID";
 
$access_token = $_SESSION['access_token'];
 
?>
 
<button id="post_button" onclick="updateStatus('<?php echo $url;?>', '<?php echo $access_token;?>', '<?php echo $app_id;?>')">Post on facebook</button>
 
</form>
```

As you can see we have set the values we need for the call, app_id and access_token and called the updateStatus javascript function with these parameters.

To get the info about the user (their facebook id we need for the url) we have queried the db. The id of the user was already stored in session (remember we did that when logging the user in) together with the access token. Using the id of the users we have sent a query to the database to get the rest of the user info.

The updateStatus function looks like this:

```
function updateStatus(url, access_token, app_id){
 
document.getElementById('post_button').disabled = true;
 
message = document.getElementById('status').value;
 
var jqxhr = $.post(url, { "access_token": access_token, "message": message, "app_id": app_id },
 
function(data) {
 
alert("Status updated successfully!");
 
document.getElementById('post_button').disabled = false;
 
document.getElementById('status').value = "";
 
});
 
}
```

We have used the jquery library to make a post request. The parameters we sent are the appid, accesstoken and the message that the user wrote. We get the text of the message by getting the value of the textarea.

```
message = document.getElementById('status').value; 
```

Using these values, we make a post request to the server and when the server returns successfully, we show a message to the user to let them know the status was posted.

We also added some code that disabled the post button while the request is made to prevent the user from clicking more than once on it and posting the same message twice. Once the request returns, the button is enabled.

## Deleting the account

We have also added a button for users to be able to delete their accounts:

```
<a onclick="return testDelete();" href="deleteAccount.php?id=<?php echo $usr[id];?>"><button>Delete account</button></a>
```

When they press the button we will show a popup box to check that they are sure they want to delete their account. We do this by calling the testDelete() js function that looks like this:

```
function testDelete()
{
return confirm("Are you sure you want to delete your account?");
}
```

After they confirm, they are redirected to the deleteAccount.php script that does the deletion. The scripts gets the user id and runs the sql query for deleting the user from our db. After deletion, the scripts redirects to the index page.

```
<?php
 
$id = $_GET['id'];
 
$q = mysql_query("delete from `YOUR_TABLE_NAME` where id=$id");
 
header('Location: index.php');
 
?>
```

And that’s it! We have learnt how to add facebook registration/login to our site! Of course, this tutorial only shows basic features, but starting from this you can add whatever features you need.

## Conclusion

I hope you enjoyed the tutorial and that you now have a better picture on how to use facebook for user registration. In case you have any questions or comments, please don’t hesitate to ask. I’d be happy to help!

## Source Code

You can check out the code on github. I have included the complete code for both parts of the tutorial. Don't forget to download the twitter bootstrap folder and place it in the same folder with my source code to view the nice layout. And if you want to test the code, you will have to replace the app_id, app_secret and db related values.
