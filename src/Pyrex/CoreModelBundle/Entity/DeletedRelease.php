<?php

namespace Pyrex\CoreModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeletedRelease.
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="raw_name_idx", columns={"rawName"})})
 * @ORM\Entity
 */
class DeletedRelease
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="rawName", type="string", length=255)
     */
    private $rawName;

    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="text", nullable=true)
     */
    private $genre;

    /**
     * @var string
     *
     * @ORM\Column(name="albumName", type="string", length=255, nullable=true)
     */
    private $albumName;

    /**
     * @var string
     *
     * @ORM\Column(name="artistName", type="text", nullable=true)
     */
    private $artistName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deletedDate", type="datetime")
     */
    private $deletedDate;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $rawName
     *
     * @return DeletedRelease
     */
    public function setRawName($rawName)
    {
        $this->rawName = $rawName;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getRawName()
    {
        return $this->rawName;
    }

    /**
     * Set genre.
     *
     * @param string $genre
     *
     * @return DeletedRelease
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre.
     *
     * @return string
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set albumName.
     *
     * @param string $albumName
     *
     * @return DeletedRelease
     */
    public function setAlbumName($albumName)
    {
        $this->albumName = $albumName;

        return $this;
    }

    /**
     * Get albumName.
     *
     * @return string
     */
    public function getAlbumName()
    {
        return $this->albumName;
    }

    /**
     * Set artistName.
     *
     * @param string $artistName
     *
     * @return DeletedRelease
     */
    public function setArtistName($artistName)
    {
        $this->artistName = $artistName;

        return $this;
    }

    /**
     * Get artistName.
     *
     * @return string
     */
    public function getArtistName()
    {
        return $this->artistName;
    }

    /**
     * Set deletedDate.
     *
     * @param \DateTime $deletedDate
     *
     * @return DeletedRelease
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get deletedDate.
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }
}
