<?php
/**
 * @author: Pyrex-Fwi
 */
namespace Pyrex\CoreModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Album
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Album
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @todo set url instead blob data
     * @var string
     *
     * @ORM\Column(name="cover", type="blob")
     */
    private $cover;


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
     * Set name
     *
     * @param string $name
     * @return Album
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set cover
     *
     * @param string $cover
     * @return Album
     */
    public function setCover($cover)
    {
        // @codeCoverageIgnoreStart
        $this->cover = $cover;
        return $this;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get cover
     *
     * @return string 
     */
    public function getCover()
    {
        // @codeCoverageIgnoreStart
        return $this->cover;
        // @codeCoverageIgnoreEnd
    }
}
