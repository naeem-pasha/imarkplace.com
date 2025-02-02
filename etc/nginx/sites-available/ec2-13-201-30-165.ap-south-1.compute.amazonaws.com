##
# You should look at the following URL's in order to grasp a solid understanding
# of Nginx configuration files in order to fully unleash the power of Nginx.
# https://www.nginx.com/resources/wiki/start/
# https://www.nginx.com/resources/wiki/start/topics/tutorials/config_pitfalls/
# https://wiki.debian.org/Nginx/DirectoryStructure
#
# In most cases, administrators will remove this file from sites-enabled/ and
# leave it as reference inside of sites-available where it will continue to be
# updated by the nginx packaging team.
#
# This file will automatically load configuration files provided by other
# applications, such as Drupal or Wordpress. These applications will be made
# available underneath a path with that package name, such as /drupal8.
#
# Please see /usr/share/doc/nginx-doc/examples/ for more detailed examples.
##

# Default server configuration
#

upstream fastcgi_backend {
	server unix:/var/run/php/php7.4-fpm.sock;
}

server {
    #default_server;
    listen 80;
    listen [::]:80;

   # root /var/www/html/imp242p1;
 #   index index.php index.html index.htm index.nginx-debian.html;

    #server_name 13.201.30.165;
    server_name ec2-13-201-30-165.ap-south-1.compute.amazonaws.com
    index index.php;  
    # server_name _;
    set $MAGE_ROOT /var/www/html/imp242p1;
    set $MAGE_MODE developer;
    
    include /var/www/html/imp242p1/nginx.conf.sample;

    location ~* \.(jpg|jpeg|png|gif|ico)$ {
	expires 7d;
	add_header Cache-Control "public, no-transform";
    }

    location ~* \.(css|js|jpg|jpeg|png|gif|ico)$ {
	root /var/www/html/imp242p1/pub/static;
	try_files $uri $uri/ =404;
    }

	
#    location / {
 #       try_files $uri $uri/ =404;
  #  }
}

# Virtual Host configuration for example.com
#
# You can move that to a different file under sites-available/ and symlink that
# to sites-enabled/ to enable it.
#
#server {
#	listen 80;
#	listen [::]:80;
#
#	server_name example.com;
#
#	root /var/www/example.com;
#	index index.html;
#
#	location / {
#		try_files $uri $uri/ =404;
#	}
#}
