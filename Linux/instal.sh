#! /bin/bash
sudo yes | apt-get install apache2
sudo a2enmod rewrite
sudo yes | apt-get install php5 libapache2-mod-php5
sudo yes | apt-get install php-apc
sudo rm /var/www/index.html
sudo yes | cp -r ../Workspace/ABB/* /var/www
sudo cp ../Workspace/ABB/.htaccess /var/www/.htaccess
sudo service apache2 restart
