[![Build Status](https://travis-ci.org/Pyrex-FWI/audio-api.svg?branch=master)](https://travis-ci.org/Pyrex-FWI/audio-api)

# Sapar Audio-api

- Symfony: 3.0

## Install dev environnement

### Pre require

- [Virtualbox](https://www.virtualbox.org) (>= 5.0.12)
- [Ansible](https://github.com/ansible/ansible) (>= 2.0.0.2)
- Vagrant

- Run ```vagrant up```
- Run ```vagrant provision``` or ```ansible-playbook -i devops/ansible/hosts devops/ansible/install.yml -v ```

## Commands

### Show configuration
`media:dump:collection --info`
### Create dump
`php bin/console media:dump:collection --dump`

`php bin/console rabbitmq:setup-fabric`
`sudo php bin/console rabbitmq-supervisor:rebuild -vvv`

`php bin/console media:dump:collection --async-insert -vvv`
`php bin/console rabbit:consumer media_create_media_reference  -vvv -w`
`php bin/console rabbit:consumer media_read_tag  -vvv -w`
`php bin/console rabbit:consumer media_update_tag  -vvv -w`

This command allow:

- Generate files collection
- Insert (all|new) or update files into database as Media enitty
- Update tag information

**Usage examples**

php app/console --dump

composer create-project -s dev pyrex-fwi/audio audio_api



https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md

remove empty dir

find ./ -name '*.DS_Store' -type f -delete && find ./ -type d -empty -delete

Syno

find ./ -name '*.DS_Store' -type f -delete && find ./ -type d -depth | xargs  rmdir

php app/console media:organize:files  ddp media_year,media_genre,media_month /volume1/archives/_DigitaldjPool/


phpunit -c app -v

phpunit -c app  src/AppBundle/Tests/Controller/ApiControllerTest.php

‘‘‘json
INSERT INTO sf_audio.Media
(artist,bpm,fullPath,title,type,provider,providerId,providerUrl,releaseDate,version,exist,fileName,score,deletedAt)
SELECT 
    artist,bpm,fullPath,title,type,provider,providerId,providerUrl,releaseDate,version,exist,fileName,score,deletedAt
FROM
    cpyree.Media m2
WHERE
    m2.provider = 5
        AND m2.providerId NOT IN (SELECT 
            m1.providerId
        FROM
            sf_audio.Media m1
        WHERE
            m1.provider = 5);
‘‘‘

RabbitMQ


#### Audio Fingerprint

- https://acoustid.org
- https://www.linuxserver.io
- https://musicbrainz.org
- http://echoprint.me

php bin/console hautelook_alice:doctrine:fixtures:load -vvv -n


generate your self ssl cert