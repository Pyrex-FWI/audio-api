<?php

namespace AppBundle\Id3;

use Sapar\Id3\Metadata\Id3Metadata;
use Sapar\Id3\Wrapper\Id3WrapperInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Id3Manager
{
    /** @var Id3WrapperInterface[] */
    private $wrappers = [];
    /** @var  LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ? $logger : new NullLogger();
    }

    /**
     * @param Id3WrapperInterface $wrapper
     */
    public function addWrapper(Id3WrapperInterface $wrapper)
    {
        $this->wrappers[] = $wrapper;
    }

    /**
     * @param Id3Metadata $id3Metadata
     *
     * @return bool
     */
    public function read(Id3Metadata $id3Metadata)
    {
        foreach ($this->wrappers as $wrapper) {
            if ($wrapper->supportRead($id3Metadata) && $wrapper->read($id3Metadata)) {
                $this->logger->info(sprintf('Read success for %s with %s', $id3Metadata->getFile(), get_class($wrapper)));

                return true;
            }
        }

        if ($this->findAvailableReaders($id3Metadata) < 1) {
            $this->logger->error(sprintf('NO READERS ARE AVAILABLE for %s', $id3Metadata->getFile()));
        }
        $this->logger->error(sprintf('Read error for %s', $id3Metadata->getFile()));

        return false;
    }

    /**
     * @param Id3Metadata $id3Metadata
     *
     * @return bool
     */
    public function write(Id3Metadata $id3Metadata)
    {
        foreach ($this->wrappers as $wrapper) {
            /** @var Id3WrapperInterface $wrapper */
            if ($wrapper->supportWrite($id3Metadata) && $wrapper->write($id3Metadata)) {
                $this->logger->info(sprintf('Write success for %s with %s', $id3Metadata->getFile(), get_class($wrapper)));

                return true;
            }
        }

        if ($this->findAvailableWriters($id3Metadata) < 1) {
            $this->logger->error(sprintf('NO WRITERS ARE AVAILABLE for %s', $id3Metadata->getFile()));
        }

        return false;
    }

    private function findAvailableReaders(Id3Metadata $id3MetaData)
    {
        $found = 0;
        foreach ($this->wrappers as $wrapper) {
            $found = $wrapper->supportRead($id3MetaData) ? $found + 1 : $found;
        }

        return $found;
    }

    private function findAvailableWriters(Id3Metadata $id3MetaData)
    {
        $found = 0;
        foreach ($this->wrappers as $wrapper) {
            $found = $wrapper->supportWrite($id3MetaData) ? $found + 1 : $found;
        }

        return $found;
    }
}
