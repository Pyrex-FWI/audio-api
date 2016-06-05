<?php
/**
 * User: chpyr
 * Date: 04/09/15
 * Time: 20:09
 */

namespace AppBundle\Tests\Faker;


use Faker\Provider\Base;

class AvItemProvider extends Base
{
    protected static $artist = [
        'Beyonce', 'Booba', 'Bruno Mars', 'Busta Rhymes', 'Bobby Brackins', 'Boosie Badazz',
        'Calvin Harris', 'Ciara', 'Cole Black',
        'Drake',
        'Eminem',
        'G Eazy',
        'J.Lo', 'Jackson 5', 'Jay-Z',
        'Keyshia Cole', 'Konshens',
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

    protected static $trackFileNameFormats = '{{id}}_{{artist}}-{{title}} ({{version}}).mp3';

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

    public function trackFileName($identifierRange = null)
    {
        $old = self::$trackId;
        if ($identifierRange) {
            self::$id = [$identifierRange[0], $identifierRange[1]];
        }
        $result = $this->generator->parse(static::$trackFileNameFormats);
        self::$trackId = $old;
        return $result;
    }
}