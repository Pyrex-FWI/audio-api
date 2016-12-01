<?php

namespace AppBundle\Service;

use Pyrex\CoreModelBundle\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Pyrex\NfoReader\Enslave;
use Pyrex\NfoReader\Jah;
use Pyrex\NfoReader\NfoReaderInterface;
use Pyrex\NfoReader\Shelter;
use Pyrex\NfoReader\Wax;
use Pyrex\NfoReader\Wre;
use Pyrex\NfoReader\Yard;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class NfoManager
{
    /**
     * @var NfoReaderInterface[]
     */
    private $readers = [];

    /**
     * NfoManager constructor.
     */
    public function __construct()
    {
        $this->readers[Jah::class] = Jah::class;
        $this->readers[Yard::class] = Yard::class;
        $this->readers[Enslave::class] = Enslave::class;
        $this->readers[Shelter::class] = Shelter::class;
        $this->readers[Wre::class] = Wre::class;
        $this->readers[Wax::class] = Wax::class;
    }

    /**
     * @param NfoReaderInterface $reader
     */
    public function addReader(NfoReaderInterface $reader)
    {
        $this->readers[get_class($reader)] = $reader;
    }

    /**
     * @param string $dirPathName
     * @return bool
     */
    public function containNfo($dirPathName)
    {
        $finder = Finder::create()->in($dirPathName)->depth(0)->name('*.nfo')->files();

        return $finder->count() == 1;
    }

    /**
     * @param string $dirPathName
     * @return mixed
     */
    public function getGenre($dirPathName)
    {
        $finder = Finder::create()->in($dirPathName)->name('*.nfo')->files();
        /** @var SplFileInfo $file */
        $file = array_values(iterator_to_array($finder))[0];

        foreach ($this->readers as $reader) {
            if ($reader::support($dirPathName)) {
                /** @var NfoReaderInterface $raeder */
                $reader = $reader::getInstance($file->getFileInfo()->getPathname());

                return $reader->getGenre();
            }
        }
    }
}
