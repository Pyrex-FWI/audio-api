<?php
/**
 * User: chpyr
 * Date: 04/09/15
 * Time: 20:09.
 */
namespace AppBundle\Faker\Provider;

use Faker\Provider\Base;
use Faker\Generator;

class MediaProvider extends Base
{
    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
        $this->generator->addProvider($this);
    }

    protected static $providerId = [
        1,
        2,
        3,
        4,
        100,
    ];

    protected static $artist = [
        'Beyonce', 'Booba', 'Bruno Mars', 'Busta Rhymes', 'Bobby Brackins', 'Boosie Badazz',
        'Calvin Harris', 'Ciara', 'Cole Black',
        'Drake',
        'Eminem',
        'G Eazy',
        'J.Lo', 'Jackson 5', 'Jay-Z',
        'Keyshia Cole', 'Konshens', 'Kim ft. Stony', 'Kalash',
        'Lil Wayne',
        'Mila J',
        'Neyo Lets Go Pville',
        'Nicki Minaj',
        'Omarion', 'Omni',
        'Pitbull', 'Pusha T',
        'Rihanna',
        'TI', 'Trey Songz',
        'Warren Allen',
    ];

    protected static $genres = [
        'Top 40',
        'Urban',
        'Other',
        'Dance',
        'Twerk',
        'Future House',
        'Deep House',
        'EDM',
        'Trap',
        'Zouk',
        'Rap',
        'Hip-Hop',
        'Ethnic'
    ];
    protected static $title = [
        'About You',
        'Bad Blood',
        'Black Heaven',
        'Get Low',
        'How Could You Forget',
        'I Need Your Love',
        'London Bridge',
        'Only Right',
        'Rich Nigga Problems',
        'Thats How Im Feelin',
        'El Micha El Party',
        'PJ All I Know',
        'How Could You Forget',
        'Heaven',
    ];

    protected static $version = [
        'Accapela',
        'Clean',
        'Dirty',
        'Instrumental',
        'Remix version',
        'Remix',
        'Quick-Clean',
        'Quick-Explicit',
        'Extended-Clean',
        'Extended-Explicit',
    ];

    protected static $bpm = [
        60,
        160,
    ];

    protected static $id = [
        1000,
        999999,
    ];

    protected static $extensions = [
        'mp3',
        'mp4'
    ];

    public static $trackFileNameFormats = '{{id}}_{{artist}}-{{title}}.{{mediaExtension}}';

    public function providerId()
    {
        return static::randomElement(static::$providerId);
    }

    public function mediaExtension()
    {
        return static::randomElement(static::$extensions);
    }
    /**
     * @example 'Rihanna'
     */
    public static function artist()
    {
        return static::randomElement(static::$artist);
    }

    public static function title()
    {
        return static::randomElement(static::$title);
    }

    public static function genre()
    {
        return static::randomElement(static::$genres);
    }

    public static function version()
    {
        return static::randomElement(static::$version);
    }

    public static function bpm()
    {
        return static::numberBetween(static::$bpm[0], static::$bpm[1]);
    }

    public static function id($min = null, $max = null)
    {
        if ($min && $max) {
            return static::numberBetween(intval($min), intval($max));
        }

        return static::numberBetween(static::$id[0], static::$id[1]);
    }

    public function mediaFileName($identifierRange = null)
    {
        $result = $this->generator->parse(static::$trackFileNameFormats);

        return $result;
    }
}
