<?php

namespace AppBundle\Service;

use Pyrex\CoreModelBundle\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class GenreStack
{
    /** @var  Registry */
    private $doctrine;
    /** @var  EntityRepository */
    private $genreRepository;
    /** @var  EntityManager */
    private $eManager;
    /** @var Genre[] */
    protected $genres = [];
    private $isLoaded = false;

    /**
     * GenreStack constructor.
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine        = $doctrine;
        $this->genreRepository = $this->doctrine->getRepository(\Pyrex\CoreModelBundle\Entity\Genre::class);
        $this->eManager        = $this->doctrine->getManager();
    }

    private function loadGenres()
    {
        $this->genres   = $this->genreRepository->findAll();
        $this->isLoaded = true;
    }

    /**
     * @param $name
     *
     * @return Genre|bool
     */
    protected function findOne($name)
    {
        if (!$this->isLoaded) {
            $this->loadGenres();
        }

        foreach ($this->genres as $genre) {
            if ($genre->getName() === $name) {
                return $genre;
            }
        }

        return false;
    }

    /**
     * @param $name
     *
     * @return Genre
     *
     * @throws \Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function create($name)
    {
        $this->eManager->getConnection()->beginTransaction();
        try {
            $genre = new Genre($name);
            $this->eManager->persist($genre);
            $this->eManager->flush();
            $this->eManager->getConnection()->commit();
            $this->genres[] = $genre;

            return $genre;
        } catch (\Exception $e) {
            $this->eManager->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * @param $name
     *
     * @return Genre|bool
     *
     * @throws Exception
     */
    public function getOrCreateIfNotExist($name)
    {
        $existGenre = $this->findOne($name);
        if (!$existGenre) {
            return $this->create($name);
        }

        return $existGenre;
    }
}
