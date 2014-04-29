comp140-grading
===============

A simple grading application to simplify participation grade collation and merging with Coursera. Created for Rice University's COMP 140.

To use this,
* Clone the repository
* Run `composer install`
* Set up a MySQL database and execute `project.sql` and `vendor/mschurr/framework/src/schema.sql`
* Place the configuration information in `config-template.php` and rename the file to `config.php`
* Run the development server for testing `php -S localhost:80 server.php`
* Deploy using HipHop `./hhvm.sh` or any PHP-enabled web server

Command line access is also available. For available commands, type `php server.php`. Use `clipapp.php` to register additional commands.
Example: `php server.php export ./grades.csv`

Requirements:
* Composer
* MySQL >= 5
* PHP >= 5.5
