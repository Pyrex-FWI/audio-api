FROM php:latest
RUN apt-get update \
&& apt-get install -y --no-install-recommends git \
ca-certificates \
wget zlib1g-dev vim \
python \
id3v2 \
mediainfo && \
wget "https://bootstrap.pypa.io/get-pip.py" && \
python get-pip.py && \
pip install eyeD3 && \
pip install beets && \
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin && mv /usr/bin/composer.phar /usr/bin/composer && \
docker-php-ext-install bcmath mbstring zip pdo pdo_mysql  && \
cd /opt && git clone https://github.com/krakjoe/apcu && \
    cd apcu && \
    phpize && \
    ./configure --enable-apcu && \
    make && make install && \
    echo "extension=apcu.so" > /usr/local/etc/php/conf.d/ext-apcu.ini && \
    cd /opt && git clone https://github.com/krakjoe/apcu-bc && \
    cd apcu-bc && \
    phpize && \
    ./configure --enable-apc && \
    make && make install && \
    echo "\nextension=apc.so" >> /usr/local/etc/php/conf.d/ext-apcu.ini && \
    rm -r /var/lib/apt/lists/*

ENV SYMFONY__DATABASE_HOST=mariadb
ENV SYMFONY__DATABASE_PORT=null
ENV SYMFONY__DATABASE_NAME=sf_audio
ENV SYMFONY__DATABASE_USER=admin-app
ENV SYMFONY__DATABASE_PASWWORD=admin-app
ENV SYMFONY__SECRET=ThisTokenIsNotSoSecretChangeIt
ENV SYMFONY__ID3TOOL__MEDIAINFO__BIN=/usr/bin/mediainfo
ENV SYMFONY__ID3TOOL__EYED3__BIN=/usr/local/bin/eyeD3
ENV SYMFONY__ID3TOOL__ID3V2__BIN=/usr/bin/id3v2
ENV SYMFONY__ID3TOOL__METAFLAC__BIN=/usr/bin/metaflac
ENV SYMFONY__AV_DISTRICT__CREDENTIALS__LOGIN=
ENV SYMFONY__AV_DISTRICT__CREDENTIALS__PASSWORD=
ENV SYMFONY__FRANCHISE_POOL__CREDENTIALS__LOGIN=
ENV SYMFONY__FRANCHISE_POOL__CREDENTIALS__PASSWORD=
ENV SYMFONY__SMASHVISION__CREDENTIALS__LOGIN=
ENV SYMFONY__SMASHVISION__CREDENTIALS__PASSWORD=
ENV SYMFONY__SMASHVISION__CREDENTIALS__LOGIN=
ENV SYMFONY__SMASHVISION__CREDENTIALS__PASSWORD=

RUN ln -s /usr/bin/mediainfo /usr/local/bin/

RUN echo "date.timezone = Europe/Paris" >/usr/local/etc/php/php.ini

#WORKDIR /usr/src/myapp

ENTRYPOINT ["php", "/var/sapar/audio-api/bin/console"]

#CMD [ "php", "./app/console" ]
#docker run -ti --rm --name=audio-api -v /Users/yemistikris/PhpstormProjects/sapar-project/audio-api:/usr/src/myapp -v "$PWD":/volume4/Pool/Franchise/Audio -v "$PWD":/volume4/Pool/SmashVision --add-host mariadb:192.168.1.21 --env-file vars  yemistikris/audio-api deejay:pool:download franchise_pool_audio --start 1 --end 30 -vv


#ocker run --rm --add-host mariadb:172.17.0.1 -it -v /volume1/homes/admin/audio-api-dev/:/var/sapar/audio-api/ -v /volume2/Pool/:/volume2/Pool/ yemistikris/audio-api:dev media:dump:c --dump -vvv

docker run --rm --add-host mariadb:172.17.0.1 -it --entrypoint /bin/bash -v /volume1/homes/admin/audio-api-dev/:/var/sapar/audio-api/ -v /volume2/Pool/:/volume2/Pool/ -v /volume1/archives:/volume1/archives/ yemistikris/audio-api:dev -c /usr/bin/id3v2


docker run --rm --add-host mariadb:172.17.0.1 \
--entrypoint /usr/bin/mediainfo \
-v /volume1/homes/admin/audio-api-dev/:/var/sapar/audio-api/ \
-v /volume2/Pool/:/volume2/Pool/ \
-v /volume1/archives/:/volume1/archives/ yemistikris/audio-api:dev --Full --Output=XML /volume1/archives/Zouk/2016/Marvin-Mes_Couleurs-\(WEB\)-FR-2016-H5N1/07-marvin-danger_feat_selebobo.mp3