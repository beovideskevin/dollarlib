# Dollarlib

Dollarlib is a simple php framework. You can see a full working example here: <a href="https://my2cents.eldiletante.com/">my2cents.eldiletante.com</a>

I developed this framkework using using WAMP. All the examples should work in an Apache virtual host; create your own and name it "dollarlib.loc". After cloning the repository create two files. The first "/.htaccess" with the code: 
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
		"DATABASE": "dollarlib",
		"USER": "yourusername",
		"PASSWORD": "yourpassword"
	},
	
	"EMAIL": {
		"SYSTEM": "dollarlib@gmail.com",
		"FROM": "Dollarlib",
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
			"action" : "login",
			"args": "{\"zip\": 33160}"
		},

		"pay": {
			"action": "pay",
			"enforce": "enforce"
		},
		
		"addcard": {
			"action": "addCard",
			"enforce": "enforce"
		},

		"thanks": {
			"action": "thanks",
			"enforce": "enforce"
		},

		"dashboard": {
			"action": "dashboard",
			"enforce": "enforce"
		},

		"contact": "contact",

		"snippets": "snippets",
		
		"logout": "logout",

		"es": {
			"redirect": "/?lan=es"
		},

		"en": {
			"redirect": "/?lan=en"
		},

		"login": {
			"redirect": "/"
		},

		"404": {
			"redirect": "/"
		}
	}
}
```
If you don't want to use a virtual host, you could just clone the repository in the root of your web documents and create the htaccess files. Create the config file but change the second line to "FILES_BASE_PATH": "/dollarlib/". Then go to /app/layouts/ and edit both "example.html" and "quotesLay.html"; every reference to an external resource should be prefixed with "/dollarlib/".

If you are using IIS or Nginx you will need to make some changes to "web.config" or the configuration files of your server (reproduce the logic in "/.htaccess" and "/public/.htaccess"). 

In order to make all the examples work you will need to import into the database the scripts located in the folder "migrartions". These were created for MySQL, if you are using PostgreSQL or somethng else, you may need to make some changes there too.

After this is done you can check the examples in the routes:
```
my2cents.loc/
my2cents.loc/snippets
my2cents.loc/contact
```


TO DO:
- [ ] Implement relationships and validations in the example. 
- [ ] Create tests with codeception.
- [ ] Improve email methods
- [ ] Add curl call to the $_ method  
