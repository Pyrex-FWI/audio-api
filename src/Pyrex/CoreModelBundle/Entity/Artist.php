<?php

namespace Pyrex\CoreModelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Artist
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="name_idx", columns={"name"})})
 * @ORM\Entity
 */
class Artist
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false)
     * @Groups({"artist-read", "media-read"})
     */
    private $name;
    /**
     * @ORM\ManyToMany(targetEntity="\Pyrex\CoreModelBundle\Entity\Media",mappedBy="artists")
     * @Groups({"artist-read"})
     **/
    private $medias;

    /**
     * Artist constructor.
     * @param null $name
     */
    public function __construct($name = null)
    {
        $this->setName($name);
        $this->medias = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @param mixed $medias
     * @return Artist
     */
    public function setMedias($medias)
    {
        $this->medias = $medias;
        return $this;
    }


}
