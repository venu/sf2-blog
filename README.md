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
Api: http://localhost/riktamBlog/web/app_dev.php/apidoc/
Admin: http://localhost/riktamBlog/web/app_dev.php/admin/

Testing
=======
After installation you will find API documentation and test console in http://localhost/riktamBlog/web/app_dev.php 
