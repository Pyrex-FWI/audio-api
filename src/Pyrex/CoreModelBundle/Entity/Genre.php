<?php

namespace Pyrex\CoreModelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Genre
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="name_idx", columns={"name"})}, options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @ORM\Entity
 */
class Genre
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
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     * @Assert\NotBlank()
     * @Groups({"genre-read", "media-read"})
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="\Pyrex\CoreModelBundle\Entity\Media",mappedBy="genres")
     * @Groups({"genre-read"})
     **/
    private $medias;

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
     * @return Genre
     */
    public function setMedias($medias)
    {
        $this->medias = $medias;
        return $this;
    }


}
