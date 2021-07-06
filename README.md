
<p  align="right">
<a  href="https://osky.com.au/"  target="_blank"><img  src="https://osky.com.au/wp-content/uploads/2020/06/cropped-osky_default.png"  width="140"></a></p>
<p  align="right"><a  href="https://www.php.net/"  target="_blank"><img  src="https://www.php.net/images/logos/new-php-logo.png"  width="130"></a>
</p>

  
## Instruction

  

Pre-requisites, need to install Apache service according to your preference, can use for example [Laragon](https://laragon.org/), and also you are required to have Reddit App, may follow bellow instruction 

**Create a Reddit App**

NOTE: This step is follow this [link](https://www.linkedin.com/pulse/reddit-api-php-justin-stolpe/)

Before we can start coding, we need to create a Reddit App. No Reddit App, no API access. In order to connect to the Reddit API you need a client id, client secret, username, and password. To get these things you need a Reddit app.

Step 1: Login to reddit and go to https://reddit.com/prefs/apps.

Step 2: Under “developer applications” click “create app”.

Step 3: Fill out the app info like name and description. Make sure to select “script” as well. You don’t need to enter an “about url” but a “redirect url” is required. You can enter anything for the “redirect url”. Then finally, click “create app”.

Step 4: Once your app is created you should see client id, client secret, and username, for connecting to the app.

**Open Project**

After done install all required services, may follow these step:

1. Clone this repo
2. Installed required Composer package
	```
	composer install
	```
3. Create new ```.env``` file, then insert the required credentials according to Reddit App you've just created
	```
	cp .env.example .env
	``` 
4. Use cmd to view the result
	```
	php osky.php reddit:search
	```