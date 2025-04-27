For start this application you need have installed docker-compose

for the first run this command on folder with docker-compose.yml: 
sudo docker-compose build

and then you can run application
sudo docker-compose up

Open browser by link http://localhost:8080

Application controlling data file age and if this file is older then 24 hour then update will be started  

for automatic file reload at 00:00 you can add this command to cron
 -  run crontab -e
 -  append to end of file:
 -  0 0 * * * /usr/bin/php /var/www/html/console-update.php

 Main setting you can see in the .env file