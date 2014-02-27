comp140-grading
===============

A simple grading application to simplify participation grade collation and merging with Coursera.

To use this,
* Clone the repository
* Run `composer install`
* Set up a MySQL database and execute `project.sql` and `vendor/mschurr/framework/src/schema.sql`
* Place the configuration information in `config-template.php` and rename the file to `config.php`
* Run the development server for testing `php -S localhost:80 server.php`
* Deploy using HipHop `./hhvm.sh` or any PHP-enabled web server

Requirements:
* Composer
* MySQL
* PHP >= 5.5
