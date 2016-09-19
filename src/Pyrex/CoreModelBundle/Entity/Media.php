<?php

namespace Pyrex\CoreModelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Media
 *
 * @ORM\Table(
 *      indexes={@ORM\Index(name="profider_filename", columns={"fileName"})},
 *      uniqueConstraints={@UniqueConstraint(name="full_file_path_md5", columns={"fullFilePathMd5"})},
 *      options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"}
 * )
 */
class Media
{
    const MEDIA_TYPE_AUDIO          = 1;
    const MEDIA_TYPE_VIDEO          = 2;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"media-read"})
     */
    protected $artist;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"media-read"})
     */
    protected $bpm;
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"media-read"})
     *
     */
    protected $fullPath;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     * @Groups({"media-read"})
     *
     */
    protected $fullFilePathMd5;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     * @Groups({"media-read"})
     *
     */
    protected $dirName;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"media-read", "artist-read", "genre-read"})
     */
    protected $title;
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"media-read"})
     */
    protected $releaseDate;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=40, nullable=true)
     * @Groups({"media-read"})
     */
    protected $version;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"media-read"})
     */
    protected $fileName;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     * @Groups({"media-read"})
     */
    protected $exist = false;
    /**
     * @var bool
     * @todo remove property and associated method
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     * @Groups({"media-read", "genre-read", "artist-read"})
     */
    protected $tagged = false;
    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"media-read"})
     */
    protected $score = 0;
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"media-read"})
     */
    protected $deletedAt;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\ManyToMany(targetEntity="\Pyrex\CoreModelBundle\Entity\Genre", inversedBy="medias", cascade={"persist", "detach", "refresh"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="media_genre",
     *      joinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection<Genre>
     * @Groups({"media-read"})
     **/
    protected $genres;
    /**
     * @ORM\ManyToMany(targetEntity="\Pyrex\CoreModelBundle\Entity\Artist", inversedBy="medias", cascade={"persist", "detach", "refresh"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="artist_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection<Artist>
     * @Groups({"media-read"})
     **/
    protected $artists;
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"media-read"})
     */
    protected $type;
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", length=4, nullable=true)
     * @Groups({"media-read", "artist-read", "genre-read"})
     */
    protected $year;
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->artists = new ArrayCollection();
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            'audio'         => self::MEDIA_TYPE_AUDIO,
            'video'         => self::MEDIA_TYPE_VIDEO,
        ];
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
     * Get artist.
     *
     * @return string
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * Set artist.
     *
     * @param string $artist
     *
     * @return Media
     */
    public function setArtist($artist)
    {
        if ($artist) {
            $this->artist = substr(trim($artist), 0, 254);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getBpm()
    {
        return $this->bpm;
    }

    /**
     * @param int $bpm
     * @return Media
     */
    public function setBpm($bpm)
    {
        if (filter_var($bpm, FILTER_VALIDATE_INT) || filter_var($bpm, FILTER_VALIDATE_FLOAT)) {
            if ($bpm <= 160 && $bpm >= 60) {
                $this->bpm = abs($bpm);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getExist()
    {
        return $this->exist;
    }

    /**
     * @param string $exist
     * @return $this
     */
    public function setExist($exist)
    {
        $this->exist = $exist;

        return $this;
    }

    /**
     * Get fullPath.
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->fullPath;
    }

    /**
     * Set fullPath.
     *
     * @param string $fullPath
     *
     * @return Media
     */
    public function setFullPath($fullPath)
    {
        $pattern = '#('.DIRECTORY_SEPARATOR.')\1+#';
        $replacement = DIRECTORY_SEPARATOR;
        $fullPath = preg_replace($pattern, $replacement, $fullPath);

        $this->fullPath = $fullPath;

        $this->exist = file_exists($fullPath) ? true : true;
        $splFile = new \SplFileInfo($this->fullPath);
        $this->setFileName($splFile->getBasename());
        $this->setFullFilePathMd5(md5($this->fullPath));
        $paths = explode('/', $splFile->getPath());
        $this->setDirName(end($paths));

        return $this;
    }

    /**
     * @return string
     */
    public function getFullFilePathMd5()
    {
        return $this->fullFilePathMd5;
    }

    /**
     * @param string $fullFilePathMd5
     * @return Media
     */
    public function setFullFilePathMd5($fullFilePathMd5)
    {
        if (strlen($fullFilePathMd5) == 32) {
            $this->fullFilePathMd5 = $fullFilePathMd5;
        }

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
     * Set title.
     *
     * @param string $title
     *
     * @return Media
     */
    public function setTitle($title)
    {
        if ($title) {
            $this->title = trim($title);
        }

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Media
     */
    public function setType($type)
    {
        if (!in_array($type, self::getTypes())) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid type. See %s', $type, self::class.'::getTypes()'));
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Get releaseDate.
     *
     * @return \DateTime
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * Set releaseDate.
     *
     * @param \DateTime $releaseDate
     *
     * @return Media
     */
    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * @param Genre $genre
     * @return $this
     */
    public function addGenre(Genre $genre)
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }

        return $this;
    }

    /**
     * @param Genre $genre
     * @return $this
     */
    public function removeGenre(Genre $genre)
    {
        if ($this->genres->contains($genre)) {
            $this->genres->removeElement($genre);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set Genres.
     * @param ArrayCollection $genres
     * @return $this
     */
    public function setGenres(ArrayCollection $genres)
    {
        $this->genres = $genres;

        return $this;
    }
    /**
     * @param Artist $artist
     * @return $this
     */
    public function addArtist(Artist $artist)
    {
        if (!$this->artists->contains($artist)) {
            $this->artists->add($artist);
        }

        return $this;
    }

    /**
     * @param Artist $artist
     * @return $this
     */
    public function removeArtist(Artist $artist)
    {
        if ($this->artists->contains($artist)) {
            $this->artists->removeElement($artist);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getArtists()
    {
        return $this->artists;
    }

    /**
     * @param ArrayCollection $artists
     * @return $this
     */
    public function setArtists(ArrayCollection $artists)
    {
        $this->artists = $artists;

        return $this;
    }

    /**
     * Get version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set version.
     *
     * @param string $version
     *
     * @return Media
     */
    public function setVersion($version)
    {
        $this->version = trim($version);

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param string $score
     * @return Media
     */
    public function setScore($score)
    {
        if (filter_var($score, FILTER_VALIDATE_INT) || filter_var($score, FILTER_VALIDATE_FLOAT)) {
            $this->score = $score;
        }
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     * @return Media
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirName()
    {
        return $this->dirName;
    }

    /**
     * @param string $dirName
     * @return Media
     */
    public function setDirName($dirName)
    {
        $this->dirName = $dirName;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUntaged()
    {
        // @codeCoverageIgnoreStart
        return !$this->tagged;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     * @return Media
     */
    public function setYear($year)
    {
        if (preg_match('/\d{4}/', $year)) {
            $this->year = $year;
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function getTagged()
    {
        // @codeCoverageIgnoreStart
        return $this->tagged;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param bool $tagged
     * @return Media
     */
    public function setTagged($tagged)
    {
        // @codeCoverageIgnoreStart
        $this->tagged = $tagged;
        
        return $this;
        // @codeCoverageIgnoreEnd
    }

}
