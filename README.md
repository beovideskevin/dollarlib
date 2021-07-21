# My2cents

My2cents is a simple php framework. You can see a full working example here: <a href="https://my2cents.eldiletante.com/">my2cents.eldiletante.com</a>

I developed this framkework using using WAMP. All the examples should work in an Apache virtual host; create your own and name it "my2cents.loc". After cloning the repository create two files. The first "/.htaccess" with the code: 
```
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteRule   ^$ public/    [L]
    RewriteRule   ((?s).*) public/$1 [L]
</IfModule>

<Files "config.json">
	Order Allow,Deny
	Deny from all
</Files>

Options -Indexes
```
And the second "/public/.htaccess" with these lines:
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^((?s).*)$ index.php?_url=$1 [QSA,L]
</IfModule>

Options -Indexes
```
After doing this add a file named "/config.json" to the root folder with the following code:
```
{
	"FILES_BASE_PATH": "/",
	
	"TEMPLATE": {
		"LANGUAGE_PATH": "app/languages/",
		"DEFAULT_LANGUAGE": "en",
		"LAYOUT_PATH": "app/layouts/",
		"DEFAULT_LAYOUT": "example"
	},
	
	"DATABASE": {
		"ADAPTER": "MYSQL",
		"HOST": "localhost",
		"PORT": "3306",
		"DATABASE": "my2cents",
		"USER": "yourusername",
		"PASSWORD": "yourpassword"
	},
	
	"EMAIL": {
		"SYSTEM": "my2cents@gmail.com",
		"FROM": "My2cents",
		"SERVER": "",
		"PORT": "",
		"USER": "",
		"PASSWORD": "",
		"LAYOUT": "email"
	},
	
	"recaptcha": {
		"secretKey": "secretKeyRecaptcha",
		"siteKey": "siteKeyRecaptcha"
	},
	
	"REGISTER": {
		"EXCEPTIONS": "",
		"FOLDERS": "app/"
	},
	
	"ROUTES": {
		"DEFAULT": {
			"class": "BTC",
			"method": "FullExample",
			"layout": "example"
		},

		"404": "notFound",
		
		"example1": "hello",
		
		"example2": {
			"redirect": "/example1",
			"hello": "hello" 
		},
		
		"example3" : {
			"action": "arguments",
			"args": "{\"test\": \"1\", \"outside\": {\"inside\": \"1\"}}"
		},
		
		"login": "login",
		
		"logout": "logout",
		
		"example4": {
			"action": "publicArea",
			"private": {
				"enforce": "enforce",
				"action": "privateArea"
			}
		},

		"ajax": {
			"action": "ajax",
			"admin": {
				"action": "helloAjax",
				"enforce": "enforce"
			}
		},
		
		"btc": {
			"refresh": {
				"class": "BTC",
				"method": "refreshBTCPrice"
			}
		}
	}
}
```
If you don't want to use a virtual host, you could just clone the repository in the root of your web documents and create the htaccess files. Create the config file but change the second line to "FILES_BASE_PATH": "/my2cents/". Then go to /app/layouts/ and edit both "example.html" and "quotesLay.html"; every reference to an external resource should be prefixed with "/my2cents/".

If you are using IIS or Nginx you will need to make some changes to "web.config" or the configuration files of your server (reproduce the logic in "/.htaccess" and "/public/.htaccess"). 

In order to make all the examples work you will need to import into the database the scripts located in the folder "migrartions". These were created for MySQL, if you are using PostgreSQL or somethng else, you may need to make some changes there too.

After this is done you can check the examples in the routes:
```
my2cents.loc/
my2cents.loc/example1
my2cents.loc/example2
my2cents.loc/example2/hello
my2cents.loc/example3
my2cents.loc/example4
my2cents.loc/example4/private
my2cents.loc/login
my2cents.loc/example4/private
my2cents.loc/logout
my2cents.loc/ajax/admin
my2cents.loc/login
my2cents.loc/ajax/admin
my2cents.loc/logout
my2cents.loc/btc
my2cents.loc/btc?lan=es
my2cents.loc/btc?lan=en
```


TO DO:
- [ ] Implement relationships and validations in the example. 
- [ ] Create tests with codeception.
- [ ] Improve email methods
- [ ] Add curl call to the $_ method  
- [ ] Add {{}} to the templates (this is another way <:/>)
