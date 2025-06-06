# DollarLib

DollarLib is a simple php lib, or a very naive and flexible framework.
I created this lib to build my [new media art gallery](https://eldiletante.com/)

The basic ideas behind this lib are:

- simple way of building website form templates
- built all the HTML from inside the php (no HTML and PHP mixed files)
- provide simple way of doing language change
- simple routing using functions and enforce the user authentication
- make querying the database simple
- make easy to send emails and do curl
- simple registration of classes and helper functions

In order to test you are going to need three files:

.htaccess

```Text
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^((?s).*)$ index.php?_url=$1 [QSA,L]
</IfModule>

<Files "config.json">
    Order Allow,Deny
    Deny from all
</Files>

<Files "migrations.sql">
    Order Allow,Deny
    Deny from all
</Files>

Options -Indexes
```

config.json

```JSON
{
    "ENV": "DEV",

    "DEV": 
    {
        "WEBSITE": "dollarlib",

        "FILES_PATH": "",

        "ASSETS_PATH": "application/assets/",

        "DATABASE": 
        {
            "ADAPTER": "mysql",
            "PORT": "3306",
            "HOST": "mysql",
            "DATABASE": "your_database",
            "USER": "your_username",
            "PASSWORD": "your_password",
            "MIGRATIONS": "migrations.sql"
        },

        "TEMPLATE": 
        {
            "LANGUAGE_PATH": "application/language/",
            "DEFAULT_LANGUAGE": "en.ini",
            "LAYOUT_PATH": "application/layout/",
            "DEFAULT_LAYOUT": "main.html"
        },

        "REGISTER":
        {
            "EXCEPTIONS": "",
            "FOLDERS": "application/",
            "VENDORS": "vendor/"
        },

        "ROUTES":
        {
            "default": "index"
        }
    },

    "PRO": {}
}
```

Remember to set these in the json or the connection to the database will fail:

```JSON
    "DATABASE": "your_database",
    "USER": "your_username",
    "PASSWORD": "your_password",
```

migrations.sql

```SQL
--
-- Basic structure to test migrations, database access, schemas, etc.
-- 
START TRANSACTION;

--
-- User table
--      id (auto-increment)
--      name
--      phone --> points to phone.id
--      created (timestamp)
--
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
    `id` int(11) NOT NULL,
    `name` varchar(256) NOT NULL,
    `phone` varchar(256) NOT NULL,
    `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `user` ADD PRIMARY KEY (`id`);
ALTER TABLE `user` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `user` (`id`, `name`, `phone`) VALUES (1, 'kevin', '555 555 5555');

--
-- Address table
--      id (auto-increment)
--      street
--      city
--      country
--      user --> points to user.id
--      created (timestamp)
--      status (active, deleted)
--
DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
    `id` int(11) NOT NULL,
    `street` varchar(256) NOT NULL,
    `city` varchar(256) NOT NULL,
    `country` varchar(256) NOT NULL,
    `user` int(11),
    `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('active', 'deleted') DEFAULT 'active'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `address` ADD PRIMARY KEY (`id`);
ALTER TABLE `address` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `address` (`street`, `city`, `country`, `user`) VALUES ('2280 sw', 'miami', 'USA', 1);
INSERT INTO `address` (`street`, `city`, `country`, `user`) VALUES ('boltana 26', 'Madrid', 'Spain', 1);

--
-- We are done :)
--
COMMIT;
```

To create the tables run:

```bash
php cli.php migrations
```

In order to run the examples copy the commented code at the top of each file into config.json.
What passes for documentation is inside the files in the form of comments.

You might also want to install PHPMailer (using composer) if you want to take full advantage of the email class.
