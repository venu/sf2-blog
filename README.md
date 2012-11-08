Intallation
===========
git clone https://github.com/venu/sf2-blog.git
curl -s http://getcomposer.org/installer | php
php composer.phar install

Setup Database
==============
php app/console doctrine:migrations:migrate
php app/console doctrine:fixtures:load --append

Testing
=============
Api: http://blog.local/apidoc/
Admin: http://blog.local/admin/
