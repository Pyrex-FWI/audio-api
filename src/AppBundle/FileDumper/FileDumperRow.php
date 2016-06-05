<?php

namespace AppBundle\FileDumper;

class FileDumperRow
{
    /** @var  \SplFileInfo */
    private $file;

    private $provider;

    public function __construct(\SplFileInfo $file, $provider)
    {
        $this->setProvider($provider);
        $this->setFile($file);
    }
    /**
     * @return \SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param \SplFileInfo $file
     *
     * @return FileDumperRow
     */
    private function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param mixed $provider
     *
     * @return FileDumperRow
     */
    private function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }
}
