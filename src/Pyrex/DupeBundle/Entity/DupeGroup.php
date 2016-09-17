<?php

namespace Pyrex\DupeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class DupeGroup.
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Pyrex\DupeBundle\Repository\DupeGroupRepository")
 */
class DupeGroup
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="DupeFile", mappedBy="dupeGroup", cascade={"persist", "remove"}, fetch="EAGER")
     *
     * @var ArrayCollection
     */
    private $dupeFiles = [];

    /**
     * @ORM\Column(type="string", length=4)
     *
     * @var string
     */
    private $extension;

    /**
     * @ORM\Column(type="integer", length=3)
     *
     * @var int
     */
    private $itemCount = 0;

    /**
     * DupeGroup constructor.
     */
    public function __construct()
    {
        $this->dupeFiles = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return DupeGroup
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDupeFiles()
    {
        return $this->dupeFiles;
    }

    /**
     * @param DupeFile[] $dupeFiles
     *
     * @return DupeGroup
     */
    public function setDupeFiles($dupeFiles)
    {
        foreach ($dupeFiles as $dupeFile) {
            $dupeFile->setDupeGroup($this);
            $this->addFile($dupeFile);
        }

        return $this;
    }

    /**
     * @param DupeFile $dupeFile
     *
     * @return $this
     */
    public function addFile(DupeFile $dupeFile)
    {
        $fileInfo = new \SplFileInfo($dupeFile->getPathFile());

        $dupeFile->setDupeGroup($this);
        $this->dupeFiles->add($dupeFile);
        $this->setExtension($fileInfo->getExtension());

        return $this;
    }

    /**
     * @param DupeFile $dupeFile
     *
     * @return $this
     */
    public function removeFile(DupeFile $dupeFile)
    {
        foreach ($this->getFiles() as $file) {
            if ($file->getId() === $dupeFile->getId()) {
                $this->dupeFiles->removeElement($file);
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getItemCount()
    {
        return $this->itemCount;
    }

    /**
     * @param int $itemCount
     *
     * @return DupeGroup
     */
    public function setItemCount($itemCount)
    {
        $this->itemCount = $itemCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     *
     * @return DupeGroup
     */
    private function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->itemCount = $this->dupeFiles->count();

        $ext = [];
        foreach ($this->dupeFiles as $file) {
            /* @var DupeFile $file */
            $fileInfo = new \SplFileInfo($file->getPathFile());
            $ext[]    = $fileInfo->getExtension();
        }

        $ext = array_unique($ext);
        if (count($ext) === 1) {
            $this->extension = $ext[0];
        }
    }
}
