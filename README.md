Intallation
===========
git clone git@78.47.162.180:ppCommunity.git
curl -s http://getcomposer.org/installer | php
php composer.phar install

Database
========
php app/console doctrine:migrations:migrate
php app/console doctrine:fixtures:load --append


Documentation
=============
php app/console doctrine:schema:update --dump-sql
php app/console doctrine:migrations:diff
php app/console doctrine:migrations:migrate
php app/console doctrine:fixtures:load --append

Testing
=======
After installation you will find API documentation and test console in http://ppcommunity.local/app_dev.php 
