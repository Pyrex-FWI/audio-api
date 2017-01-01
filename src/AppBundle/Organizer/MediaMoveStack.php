<?php

namespace AppBundle\Organizer;

use AppBundle\Entity\Media;
use Deejay\Id3ToolBundle\Wrapper\Id3Manager;

class MediaMoveStack
{
    /** @var \SplFileInfo */
    private $fsys;

    private $origin;

    private $finalPathFileParts = [];

    private $tagInfo;

    private $outPath;
    /** @var Media */
    private $media;
    /** @var Id3Manager */
    private $Id3ToolManager;

    private $tagIsReaded = false;

    public function __construct($outPath, Media $media, Id3Manager $Id3Tool)
    {
        $this->media = $media;
        $this->Id3ToolManager = $Id3Tool;
        $file = $this->media->getFullPath();
        $this->setOrigin($this->media->getFullPath());
        if (!is_file($file)) {
            throw new \Exception(sprintf('File %s not exist', $file));
        }
        $this->fsys = new \SplFileInfo($file);
        $this->setOrigin($file);
        $this->outPath = $outPath;
    }

    /**
     * @return mixed
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param mixed $origin
     *
     * @return MediaMoveStack
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * @param $part
     *
     * @return $this
     */
    public function addPathPart($part)
    {
        if (is_array($part)) {
            $part = $part[0];
        }
        if (preg_match('/\./', $part) > 0) {
            return $this;
        }

        $this->finalPathFileParts[] = trim($part);

        return $this;
    }

    public function getPathParts()
    {
        return $this->finalPathFileParts;
    }

    /**
     * @param mixed $destination
     *
     * @return MediaMoveStack
     */
    public function getFinalDestination()
    {
        $destination = sprintf(
            '%s%s%s%s%s',
            $this->outPath,
            DIRECTORY_SEPARATOR,
            implode(DIRECTORY_SEPARATOR, $this->finalPathFileParts),
            DIRECTORY_SEPARATOR,
            $this->getFsys()->getBasename()
        );
        $pattern = '#('.DIRECTORY_SEPARATOR.')\1+#';
        $replacement = DIRECTORY_SEPARATOR;

        return preg_replace($pattern, $replacement, $destination);
    }

    /**
     * @return \Deejay\Id3ToolBundle\Wrapper\Id3ReaderWrapperInterface
     */
    public function getTagInfo()
    {
        if ($this->tagIsReaded === false) {
            $this->Id3ToolManager->readTags($this->media->getFullPath());
        }

        return $this->Id3ToolManager->getReaderWrapper();
    }

    /**
     * @return \SplFileInfo
     */
    public function getFsys()
    {
        return $this->fsys;
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }
}
