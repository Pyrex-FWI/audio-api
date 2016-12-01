<?php

namespace Pyrex\CoreModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RadioHit.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pyrex\CoreModelBundle\Repository\RadioHitRepository")
 */
class RadioHit
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
     * @ORM\Column(name="artist", type="string", length=255)
     */
    private $artist;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="Genre", cascade={"persist", "detach", "refresh"})
     * @ORM\JoinTable(name="radiohit_genre",
     *      joinColumns={@ORM\JoinColumn(name="radiohit_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id")}
     *      )
     *
     * @var ArrayCollection<Genre>
     **/
    protected $genres;

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
     * Set artist.
     *
     * @param string $artist
     *
     * @return RadioHit
     */
    public function setArtist($artist)
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * Get artist.
     *
     * @return string
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return RadioHit
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return RadioHit
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->genres = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created = new \DateTime('now');
    }

    /**
     * Add genres.
     *
     * @param \Pyrex\CoreModelBundle\Entity\Genre $genres
     *
     * @return RadioHit
     */
    public function addGenre(\Pyrex\CoreModelBundle\Entity\Genre $genres)
    {
        $this->genres->add($genres);

        return $this;
    }

    /**
     * Remove genres.
     *
     * @param \Pyrex\CoreModelBundle\Entity\Genre $genres
     */
    public function removeGenre(\Pyrex\CoreModelBundle\Entity\Genre $genres)
    {
        $this->genres->removeElement($genres);
    }

    /**
     * Get genres.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenres()
    {
        return $this->genres;
    }
}
