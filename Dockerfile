FROM php:7.0.10-apache
RUN apt-get update \
&& apt-get install -y --no-install-recommends git \
wget zlib1g-dev vim sudo \
python \
id3v2 \
mediainfo && \
wget "https://bootstrap.pypa.io/get-pip.py" && \
python get-pip.py && \
pip install eyeD3 && \
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



RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/web#g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/web#g' /etc/apache2/apache2.conf


WORKDIR /var/www/html/


#COPY ./app/config/parameters.yml.dist /var/www/html/app/conf/


ENV AUDIO_API_VERSION=master
RUN ln -s /usr/bin/mediainfo /usr/local/bin/
#RUN whereis mediainfo

#RUN whereis id3v2

#VOLUME /volume4/Pool/AvDistrict
#VOLUME /volume4/Pool/Franchise/Audio/Sandbox
#VOLUME /volume4/Pool/SmashVision


#COPY ./ /var/www/html/
#RUN rm -r ./* && git clone --branch $AUDIO_API_VERSION https://github.com/Pyrex-FWI/audio_api.git .
#RUN git tag -l
#RUN git checkout $AUDIO_API_VERSION
#RUN git config --global user.email "you@example.com"
#RUN git config --global user.name "Your Name"

#RUN chown -R www-data: /var/www/html && chmod -R 777 /var/www/html

#RUN ls -al /var/www/html/ 

#RUN find . -maxdepth 1 -mindepth 1 -type d -exec touch {} \+

#RUN ls -al /var/www/html/app/config/parameters.yml
#RUN sed -i '/id3tool.mediainfo.bin/d' /var/www/html/app/config/parameters.yml
#RUN sed -i '/id3tool.id3v2.bin/d' /var/www/html/app/config/parameters.yml
#RUN sed -i '/id3tool.eyed3.bin/d' /var/www/html/app/config/parameters.yml
#RUN sed -i '/id3tool.metaflac.bin/d' /var/www/html/app/config/parameters.yml

#RUN grep -ri 'id3tool.mediainfo.bin' /var/www/html/app/

RUN export
#ADD app/config/parameters.yml app/config/

#RUN mkdir -p /volume4/Pool/Franchise/Audio/Sandbox && mkdir -p /volume4/Pool/SmashVision/Sandbox

#RUN composer install -nq --no-dev --optimize-autoloader --prefer-source
#RUN composer install -nq --no-dev --optimize-autoloader

#RUN rm -rf /var/www/html/app/cache/* && rm -rf /var/www/html/app/logs/* && \
#php app/console cache:warmup && \
#chown -R www-data: /var/www/html/app/cache/ && chown -R www-data: /var/www/html/app/logs/

RUN echo "date.timezone = Europe/Paris" >/usr/local/etc/php/php.ini
#RUN usermod -u 1024 www-data
#find / -user <OLDUID> -exec chown -h <NEWUID> {} \;
