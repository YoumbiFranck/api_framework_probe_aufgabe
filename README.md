## plugin documentations
* routing: https://github.com/skipperbent/simple-php-router
* .env support: https://github.com/vlucas/phpdotenv
* crypto: https://github.com/defuse/php-encryption
* database:
  * https://github.com/illuminate/database
  * https://laravel.com/docs/9.x/database

## setup
* change package name in composer.json
* copy ``.env.example`` to ``.env`` and enter your settings
* run ``composer install`` to get all dependencies
* if you cloned this from git, remove the .git folder and make a seperate repository for your project
* setup your webserver to use the "/public" directory as the main directory (example vhost config for apache on xampp):
```
<VirtualHost *:80>
    ServerName project.localhost
    DocumentRoot C:\xampp\htdocs\project\public
</VirtualHost>
```
