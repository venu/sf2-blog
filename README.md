Symfony2 Blog Application
=========================
This is simple demo of creating blog using symfony2 (2.1) & with some good bundles. 

* FOSUserBundle
* FosRestBundle
* NelmioApiDocBundle
* JMSSerializerBundle
* TinymceBundle 
* Doctrine Bundles (ORM, fixtures, Migrations etc)
* and some other good bundles

Features
--------
* Anonymous/Logged in user can view posts.
* User can register, log-in etc
* User can post comments.
* User can like/dislike posts.
* Admin can login.
* Admin can manage (create/view/edit/delete) posts.
* Admin can manage comments.

Intallation
-----------
* git clone git@github.com:venu/sf2-blog.git
* curl -s http://getcomposer.org/installer | php
* php composer.phar install
* Rename app/config/paramters.dist.yml to app/config/paramters.yml
* Modify app/config/paramters.yml file with your database settings.
* Create virtualhost for blog.local. And that domain in host file.

Setup Database
--------------
* php app/console doctrine:migrations:migrate
* php app/console doctrine:fixtures:load --append

Testing
-------
* Blog: http://blog.local/
* Api: http://blog.local/apidoc/
* Admin: http://blog.local/admin/
