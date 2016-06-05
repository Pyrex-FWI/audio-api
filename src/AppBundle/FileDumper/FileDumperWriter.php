<?php

/*
 * This file is part of the Audio Api.
 *
 * (c) Christophe Pyree
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\FileDumper;

/**
 * Class FileDumperWriter
 * @package AppBundle\FileDumper
 */
class FileDumperWriter
{
    /** @var   */
    private $cmdPatern;
    /** @var   */
    private $outpuPatht;
    /** @var array  */
    private $paths = [];
    /** @var  string. */
    private $filePatern;
    /** @var  string Provider name. */
    private $provider;

    private $name;

    /**
     * FileDumperWriter constructor.
     * @param $name
     * @param $provider
     * @param $cmdPatern
     * @param array $paths
     * @param $filePatern
     * @param $output
     */
    public function __construct($name, $provider, $cmdPatern, $paths = [], $filePatern, $output)
    {
        $this->name = $name;
        $this->provider = $provider;
        $this->cmdPatern = $cmdPatern;
        $this->outpuPatht = $output;
        $this->filePatern = $filePatern;
        $this->setPaths($paths);
    }

    /**
     * Execute find and compile result into file.
     */
    public function run()
    {
        foreach ($this->paths as $path) {
            shell_exec($this->getCmd($path));
        }
    }

    /**
     * Set paths.
     * @param array $paths
     */
    private function setPaths($paths)
    {
        foreach ($paths as $path) {
            $path = preg_replace('#'.DIRECTORY_SEPARATOR.'+#', DIRECTORY_SEPARATOR, $path);
            if (in_array($path, $this->paths)) {
                continue;
            }
            $this->paths[] = $path;
        }
    }

    /**
     * Build and return command line stringfro $path
     * @param $path
     * @return string
     */
    public function getCmd($path)
    {
        return sprintf($this->cmdPatern.' >> %s', escapeshellarg($path), $this->filePatern, $this->provider, escapeshellarg($this->getFilePathName()));
    }

    /**
     * Return ouput filename.
     * @return string
     */
    public function getFilePathName()
    {
        return sprintf('%s'.DIRECTORY_SEPARATOR.'%s_%s.txt', $this->outpuPatht, $this->name, $this->provider);
    }

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Get Name.
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get paths.
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }
}
