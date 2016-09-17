<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Provider\AvDistrictMedia;
use AppBundle\Entity\Provider\DigitalDjPoolMedia;
use AppBundle\Entity\Provider\FranchiseAudioMedia;
use AppBundle\Entity\Provider\FranchiseVideoMedia;
use AppBundle\Entity\Provider\SmashVidzMedia;
use DeejayPoolBundle\Entity\AvdItem;
use DeejayPoolBundle\Entity\DdpItem;
use DeejayPoolBundle\Entity\FranchisePoolItem;
use DeejayPoolBundle\Entity\SvItem;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Media.
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="provider", type="integer", fieldName="provider")
 * @ORM\DiscriminatorMap({
 *          100 = "AppBundle\Entity\Media",
 *          1   = "AppBundle\Entity\Provider\DigitalDjPoolMedia",
 *          2   = "AppBundle\Entity\Provider\AvDistrictMedia",
 *          3   = "AppBundle\Entity\Provider\FranchiseAudioMedia",
 *          4   = "AppBundle\Entity\Provider\FranchiseVideoMedia",
 *          5   = "AppBundle\Entity\Provider\SmashVidzMedia"
 *     })
 * @ORM\Entity(repositoryClass="AudioCoreEntity\Repository\MediaRepository")
 */
class Media extends \AudioCoreEntity\Entity\Media
{
    const PROVIDER_DIGITAL_DJ_POOL = 1;
    const PROVIDER_AV_DISTRICT     = 2;
    const PROVIDER_FRP_AUDIO       = 3;
    const PROVIDER_FRP_VIDEO       = 4;
    const PROVIDER_SMASHVISION     = 5;
    const PROVIDER_MEDIA           = 100;

    public static $providerMapCodeToId = [
        'av_district' => self::PROVIDER_AV_DISTRICT,
        'frp_video'   => self::PROVIDER_FRP_VIDEO,
        'frp_audio'   => self::PROVIDER_FRP_AUDIO,
        'ddp'         => self::PROVIDER_DIGITAL_DJ_POOL,
        'sv'          => self::PROVIDER_SMASHVISION,
    ];

    public static $providerMapIdToClass = [
        self::PROVIDER_DIGITAL_DJ_POOL => DigitalDjPoolMedia::class,
        self::PROVIDER_AV_DISTRICT     => AvDistrictMedia::class,
        self::PROVIDER_FRP_AUDIO       => FranchiseAudioMedia::class,
        self::PROVIDER_FRP_VIDEO       => FranchiseVideoMedia::class,
        self::PROVIDER_SMASHVISION     => SmashVidzMedia::class,
        self::PROVIDER_MEDIA           => self::class,
    ];

    public static function getProviders()
    {
        return self::$providerMapCodeToId;
    }
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"media-read"})
     */
    protected $providerUrl;

    /**
     * @var int
     *
     * ORM\Column(name="provider", type="integer")
     * @Groups({"media-read"})
     */
    protected $provider;

    /**
     * @var int
     *
     * @todo change property name to externalId and update related method
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Groups({"media-read"})
     */
    protected $providerId;

    /**
     * Get downloadlink.
     *
     * @return string
     */
    public function getProviderUrl()
    {
        return $this->providerUrl;
    }

    /**
     * Set downloadlink.
     *
     * @param string $providerUrl
     *
     * @return Media
     */
    public function setProviderUrl($providerUrl)
    {
        if (filter_var($providerUrl, FILTER_VALIDATE_URL)) {
            $this->providerUrl = $providerUrl;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getProviderId()
    {
        return $this->providerId;
    }

    /**
     * @param int $providerId
     *
     * @return Media
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;

        return $this;
    }
    /**
     * Get provider.
     *
     * @return int
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @todo refactor into subClass (audio implementation)
     *
     * @param $sampleItem
     *
     * @return int|null
     */
    public static function getProviderFromItem($sampleItem)
    {
        // @codeCoverageIgnoreStart
        $provider = null;
        if ($sampleItem instanceof AvdItem) {
            $provider = self::PROVIDER_AV_DISTRICT;
        } elseif ($sampleItem instanceof FranchisePoolItem) {
            if ($sampleItem->isAudio()) {
                $provider = self::PROVIDER_FRP_AUDIO;
            } elseif ($sampleItem->isVideo()) {
                $provider = self::PROVIDER_FRP_VIDEO;
            }
        } elseif ($sampleItem instanceof SvItem) {
            $provider = self::PROVIDER_SMASHVISION;
        } elseif ($sampleItem instanceof DdpItem) {
            $provider = self::PROVIDER_DIGITAL_DJ_POOL;
        }

        return $provider;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param $providerId
     *
     * @return string
     */
    public static function getProviderEntityClass($providerId)
    {
        if (isset(self::$providerMapIdToClass[$providerId])) {
            return self::$providerMapIdToClass[$providerId];
        }
    }

    /**
     * @todo Refactor. Implementation specific
     *
     * @return $this
     */
    public function updateProviderId()
    {
        // @codeCoverageIgnoreStart
        $fileName = $this->getFileName();
        $provider = $this->getProvider();
        $patern   = '/^(?P<providerId>\d{1,9})\_/';

        if ($fileName && $provider && in_array($provider, $this->getProviders())) {
            if ($provider === self::PROVIDER_SMASHVISION) {
                $patern = '/^(?P<providerId>\d{1,9}\_\d{1,9})\_/';
            }

            if (preg_match($patern, $fileName, $matches)) {
                $this->setProviderId($matches['providerId']);
            }
        }

        return $this;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        $this->updateProviderId();

        return $this;
    }

    /**
     * @return array
     * @Groups({"media-read"})
     */
    public function getProviderCode()
    {
        $key = array_search($this->getProvider(), $this->getProviders());

        return $key;
    }

    /**
     * @return array
     */
    public static function getProviderClass($provider)
    {
        $key = self::$providerMapIdToClass[$provider];

        return $key;
    }
}
