# Robot House

Mobile friendly wrapper app to control hand-picked 
devices on a Vera Lite

Feel free to use. You'll need:

* A Vera Lite (maybe 3) with a Mios account attached to it
* A web server with modern PHP server with Memcached, SQLite and composer. A $5 droplet from [Digital Ocean](https://www.digitalocean.com/?refcode=57135b769bba) is ideal for this
* Four CSV files describing the devices, rooms, scenes and shortcuts you want to see from this app. Review the [seeding migration](https://github.com/dmlogic/robot-house/blob/master/migrations/20150501132907_seed_data.php) to establish the format for these files
* An `environment.php` file based on the [sample](https://github.com/dmlogic/robot-house/blob/master/environment.sample.php)
* Be sure to set the `AUTH_PWD` value as per instructions 

Once you've got all that, run `composer install` to get the dependencies. Then install the database with `./vendor/robmorgan/phinx/bin/phinx migrate`. (worth reading up on [Phinx](https://phinx.org/) if you ever need to adjust your db).

Browse to your site and all should be working.