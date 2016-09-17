<?php

namespace Pyrex\DupeBundle;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Pyrex\DupeBundle\Entity\DupeFile;
use Pyrex\DupeBundle\Entity\DupeGroup;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DatabaseImport
{
    /** @var  string */
    private $dupeDumpFile;
    /** @var  EventDispatcherInterface */
    private $eventDispatcher;
    /** @var  LoggerInterface */
    private $logger;
    /** @var array  */
    private $allowedExtensions = [];

    public function __construct($dupeDumpFile, EventDispatcherInterface $eventDispatcher, LoggerInterface $logger = null)
    {
        $this->dupeDumpFile    = $dupeDumpFile;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger          = $logger ? $logger : new NullLogger();
    }

    public function read()
    {
        $handle = fopen($this->dupeDumpFile, 'r');
        while (!feof($handle)) {
            $line = fgets($handle, 50000);
            if (!$line) {
                continue;
            }
            $data = $this->parseLine($line);

            if (count($data) > 0) {
                $dupeGroup = new DupeGroup();
                $dupeGroup->setDupeFiles($data);
                $dupeGroupEvent = new DupeGroupEvent($dupeGroup);
                $this->eventDispatcher->dispatch(Event::DUMP_READ_DUPLICATE_GROUP, $dupeGroupEvent);
            }
        }
        fclose($handle);
    }

    /**
     * @return array
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    /**
     * @param $allowedExtensions
     *
     * @return $this
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;

        return $this;
    }

    /**
     * @param $line
     *
     * @return array
     */
    private function parseLine($line)
    {
        $data = (array_map(
            function ($item) {
                $item = '/'.ltrim($item, '/');
                $obj = new DupeFile();
                $obj->setPathFile($item);
                if ($this->itemIsAllowed($item)) {
                    return $obj;
                }
            },
            array_map('trim', preg_split('#\s\/#', $line))
        ));

        return array_filter($data);
    }

    private function itemIsAllowed($item)
    {
        $allowed = true;

        if ($this->getAllowedExtensions()) {
            $fileInfo = new \SplFileInfo($item);
            $allowed  = in_array($fileInfo->getExtension(), $this->getAllowedExtensions());
        }

        return $allowed;
    }
}
