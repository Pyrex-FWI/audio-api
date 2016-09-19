Config:

    pyrex_dupe:
        dupe_dump_file: '/volume4/Pool/SmashVision/fdupe_log.txt'
        database:
            path: '%kernel.root_dir%//dupe.db'

Index fdupe:

    php app/console dupe:update:db --em pyrex_dupe

Go to /dupe


        docker build --rm -t yemistikris/pyrex-dupe .
        docker create --name pyrex-dupe -ti -v /Volumes:/Volumes -v /Users/yemistikris/Music/ddj/:Users/yemistikris/Music/ddj/ yemistikris/pyrex-dupe
        docker start pyrex-dupe
        docker exec  -ti pyrex-dupe fdupes -r1nA /Volumes/Extend > /Volumes/Extend/fdupes.log
        docker exec  -ti pyrex-dupe fdupes -r1nA /Volumes/SSD_MAC/ddj/ > /Volumes/SSD_MAC/ddj/fdupes.log
        
docker run  --name=pyrex-dupe --add-host mariadb:$MARIADB_HOST  --privileged -v /volume3/docker/my-app/audio-api:/var/sapar/audio-api/ --env-file /volume3/docker/private-data/vars --env SYMFONY_ENV="dev" -v /volume4/Pool/:/volume4/Pool -v /volume3/web/sapar-project/audio-api/:/var/www/html/ -v /volume1/:/volume1 -p 8082:80 -d  yemistikris/pyrex-dupe 
usermod -u 1024 www-data
b6841b1:/var/www/html# cat /etc/passwd | grep 1023
root@188bfb6841b1:/var/www/html# usermod -u 1023 www-data
root@188bfb6841b1:/var/www/html# cat /etc/passwd | grep www
www-data:x:1023:33:www-data:/var/www:/usr/sbin/nologin
root@188bfb6841b1:/var/www/html# groupmod -g 1023 www-data
root@188bfb6841b1:/var/www/html#