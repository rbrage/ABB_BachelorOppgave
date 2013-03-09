#! /bin/bash
sudo apt-get install apache2
sudo a2enmod rewrite
sudo apt-get install php5 libapache2-mod-php5
sudo apt-get install php-apc
sudo service apache2 restart