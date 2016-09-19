<?php


namespace Pyrex\CoreModelBundle\Tests;

require_once 'autoload.php';

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Parser;
use Composer\Autoload\ClassLoader;

class Bootstrap {

    static public function getConf()
    {
        $yaml = new Parser();
        return $yaml->parse(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'/config/parameters.yml'));
    }

    static public function GetEntityManager()
    {

        $paths = array(__DIR__ . DIRECTORY_SEPARATOR . '../src/Entity');
        $isDevMode = false;
        $params = self::getConf();

        $dbParams = array(
            'driver'    => $params['database_driver'],
            'user'      => $params['database_user'],
            'password'  => $params['database_password'],
            'dbname'    => $params['database_dbname'],
        );

        $cache = new \Doctrine\Common\Cache\ArrayCache();

        $reader = new AnnotationReader();
        $driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($reader, $paths);

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $config->setMetadataCacheImpl( $cache );
        $config->setQueryCacheImpl( $cache );
        $config->setMetadataDriverImpl( $driver );

        $entityManager = EntityManager::create($dbParams, $config);

        $platform = $entityManager->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');



        return $entityManager;
    }
}
