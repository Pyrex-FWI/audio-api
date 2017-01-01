<?php
/**
 * Date: 06/09/15
 * Time: 11:56.
 */

namespace AppBundle\Organizer;

use AppBundle\Entity\Media;
use AppBundle\Organizer\Rules\RuleInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;

class MediaOrganizerManager
{
    /** @var array RuleInterface[] */
    private $rules = [];
    /** @var EventDispatcher */
    protected $eventDispatcher;
    /**
     * @var Logger;
     */
    protected $logger;

    public function __construct(
        $eventDispatcher,
        Logger $logger = null)
    {
        $this->logger = $logger ? $logger : new NullLogger();
        $this->eventDispatcher = $eventDispatcher;
    }

    public function addRule(RuleInterface $rule)
    {
        $this->rules[$rule->getName()] = $rule;
    }

    /**
     * @param $name
     *
     * @return RuleInterface
     *
     * @throws \Exception
     */
    public function get($name)
    {
        if (!isset($this->rules[$name])) {
            throw new \Exception(sprintf('%s rule not Found', $name));
        }

        return $this->rules[$name];
    }

    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param $outPath
     * @param $file
     * @param $rules
     */
    public function apply($outPath, Media $media, $rules)
    {
        $moveInstruc = new MediaMoveStack($outPath, $media);

        foreach ($rules as $rule) {
            $this->get($rule)->apply($moveInstruc);
        }

        if ($moveInstruc->getOrigin() === $moveInstruc->getFinalDestination()) {
            //$this->logger->info(sprintf('Skip #%s, already done', $media->getId()));
            return;
        }

        $destinationDir = (new \SplFileInfo($moveInstruc->getFinalDestination()))->getPath();

        $fSys = new Filesystem();

        try {
            if (count($rules) != count($moveInstruc->getPathParts())) {
                $this->logger->warning(sprintf('Possible error because final part count doesn\'t equal rules count. %s -> %s',
                    $moveInstruc->getOrigin(), $moveInstruc->getFinalDestination()), [$moveInstruc, $media]);
            }
            $fileNameOrg = basename($moveInstruc->getOrigin());
            $fileNameDest = basename($moveInstruc->getFinalDestination());
            if ($fileNameOrg !== $fileNameDest) {
                throw new \Exception(sprtinf('File name \'%s\' are modified in \'%s\' for %s file', $fileNameOrg, $fileNameDest, $moveInstruc->getOrigin()));
            }

            if (!is_dir($destinationDir)) {
                $fSys->mkdir($destinationDir);
            }

            $fSys->rename($moveInstruc->getOrigin(), $moveInstruc->getFinalDestination(), true);
            $this->logger->info('Move: '.$moveInstruc->getOrigin().' to '.$moveInstruc->getFinalDestination(), [$moveInstruc, $media]);
            $media->setFullPath($moveInstruc->getFinalDestination());

            return true;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('can move  %s to %s',
                $moveInstruc->getOrigin(),
                $moveInstruc->getFinalDestination()
            ), [$moveInstruc, $media]);
            $this->logger->error($e->getMessage());

            return false;
        }
    }
}
