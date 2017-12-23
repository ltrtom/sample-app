### 1 - Setup autoloader
` php composer.phar install`

### 2 - Load the SQL schema 
` mysql database_name < init.sql`

### 3 - Replace the database credentials
Change all variables by your parameters in web/app.php

`$app->initDatabase('<host>', '<database_name>', '<database_user>', '<database_password>');`
 
### 4 - Run the server
`cd web && php -S localhost:8001`

### 5 - Test it
[http://localhost:8001/](http://localhost:8001/)
 