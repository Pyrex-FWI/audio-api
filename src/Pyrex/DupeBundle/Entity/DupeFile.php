<?php

namespace Pyrex\DupeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DupeFile.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pyrex\DupeBundle\Repository\DupeFileRepository")
 */
class DupeFile
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="string", length=30)
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="DupeGroup", inversedBy="dupeFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dupeGroup;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $parentFolder;

    /**
     * @var string
     *
     * @ORM\Column(name="pathFile", type="text")
     */
    private $pathFile;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleteFlag", type="boolean", nullable=true )
     */
    private $deleteFlag = false;

    /**
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function generateId($pathFile)
    {
        return md5($pathFile);
    }

    /**
     * Set pathFile.
     *
     * @param string $pathFile
     *
     * @return DupeFile
     */
    public function setPathFile($pathFile)
    {
        $pathFile       = stripslashes($pathFile);
        $this->pathFile = $pathFile;
        $this->id       = $this->generateId($pathFile);
        $folder         = explode('/', $pathFile);
        $length         = count($folder);
        if ($length > 1) {
            $this->setParentFolder($folder[$length - 2]);
        }

        return $this;
    }

    /**
     * Get pathFile.
     *
     * @return string
     */
    public function getPathFile()
    {
        return $this->pathFile;
    }

    /**
     * @return mixed
     */
    public function getDupeGroup()
    {
        return $this->dupeGroup;
    }

    /**
     * @param mixed $dupeGroup
     *
     * @return DupeFile
     */
    public function setDupeGroup($dupeGroup)
    {
        $this->dupeGroup = $dupeGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getParentFolder()
    {
        return $this->parentFolder;
    }

    /**
     * @param string $parentFolder
     *
     * @return DupeFile
     */
    public function setParentFolder($parentFolder)
    {
        $this->parentFolder = $parentFolder;

        return $this;
    }

    /**
     * @return \SplFileInfo
     */
    public function getFile()
    {
        return new \SplFileInfo($this->getPathFile());
    }

    /**
     * @return int
     */
    public function getCrc32()
    {
        try {
            return crc32(file_get_contents($this->getPathFile()));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * @return int
     */
    public function getSize()
    {
        try {
            return $this->getFile()->getSize();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return bool
     */
    public function isDeleteFlag()
    {
        return $this->deleteFlag;
    }

    /**
     * @param bool $deleteFlag
     *
     * @return DupeFile
     */
    public function setDeleteFlag($deleteFlag)
    {
        $this->deleteFlag = $deleteFlag;

        return $this;
    }
}
