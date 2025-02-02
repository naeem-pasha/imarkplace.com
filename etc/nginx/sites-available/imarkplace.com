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

    # root /var/www/html/imp242p1;
    # index index.php index.html index.htm index.nginx-debian.html;

    #server_name 13.201.30.165;
    server_name imarkplace.com www.imarkplace.com
    index index.php;  

    # Block all REST API requests
#    location ~ ^/rest {
 #       return 403;
  #  }
    
    # Block specific IP addresses
    deny 103.12.122.64;  # Another IP to block
    deny  182.180.57.113;  # Another IP to block
    deny 134.209.163.226;
    deny 52.137.111.65;
    deny 13.233.230.202;
    deny 23.251.32.180;
    deny 23.251.35.130;     
    deny 148.113.143.77;
    deny 23.251.35.135;
    deny 23.251.35.122;
    deny 192.168.1.1;
    deny 203.0.113.0/24; # Block an entire subnet

   # Allow all other IPs
    allow all;

    # server_name _;
    set $MAGE_ROOT /var/www/html/imp242p1;
   # set $MAGE_MODE developer;
    
    include /var/www/html/imp242p1/nginx.conf.sample;

   # error_page 404 /404.html;
    #  location = /404.html {
     # root /var/www/html/imp242p1;
     # internal;
    #}

#    limit_req_zone $binary_remote_addr zone=one:10m rate=1r/s;

#     location /b2bmarketplace/supplier/ {
 #      limit_req zone=one burst=5;
  #   }

#     location /customer/account/ {
 #      limit_req zone=one burst=5;
  #   }
   
    location ~* \.(jpg|jpeg|png|gif|ico)$ {
	expires 7d;
	add_header Cache-Control "public, no-transform";
    }

    location ~* \.(css|js|jpg|jpeg|png|gif|ico)$ {
	root /var/www/html/imp242p1/pub/static;
	try_files $uri $uri/ =404;
    }

    location /products {
      return 301 https://imarkplace.com;
    }	
    #    location / {
    #       try_files $uri $uri/ =404;
    #  }

    listen [::]:443 ssl ipv6only=on; # managed by Certbot
    listen 443 ssl; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/imarkplace.com/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/imarkplace.com/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem; # managed by Certbot


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


server {

    if ($host = www.imarkplace.com) {
        return 301 https://$host$request_uri;
    } # managed by Certbot

    if ($host = imarkplace.com) {
        return 301 https://$host$request_uri;
    } # managed by Certbot

    listen 80;
    listen [::]:80;
    server_name imarkplace.com www.imarkplace.com
    index index.php;
    return 404; # managed by Certbot
}
