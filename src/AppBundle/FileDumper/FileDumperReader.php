<?php

namespace AppBundle\FileDumper;

class FileDumperReader implements \Iterator, \Countable
{
    private $filePath;
    private $handle;
    private $pos = 0;
    private $count;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->handle = fopen($filePath, 'r');
    }
    public function __destruct()
    {
        if ($this->handle) {
            fclose($this->handle);
        }
    }

    /**
     * Count elements of an object.
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     *             </p>
     *             <p>
     *             The return value is cast to an integer.
     */
    public function count()
    {
        if (!$this->count) {
            $output = null;
            $return = null;
            $match = null;
            $com = (sprintf('wc -l %s', $this->filePath));
            exec($com, $output, $return);
            $output = (trim($output[0]));
            $patern = sprintf('#^(?P<lines>\d{1,8})#', $this->filePath);
            preg_match($patern, $output, $match);

            $this->count = isset($match['lines']) ? intval($match['lines']) : 0;
        }

        return $this->count;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element.
     *
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return FileDumperRow|null Can return any type.
     */
    public function current()
    {
        if (($line = fgets($this->handle)) !== false) {
            ++$this->pos;
            preg_match('/^"(.*)","(\d{1,3})"$/', $line, $matches);
            if (count($matches) == 0) {
                return;
            }
            $file = new \SplFileInfo($matches[1]);
            $provider = $matches[2];
            $line = trim($line);
            $lineObject = (new FileDumperRow($file, $provider));

            return $lineObject;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element.
     *
     * @link http://php.net/manual/en/iterator.next.php
     */
    public function next()
    {
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element.
     *
     * @link http://php.net/manual/en/iterator.key.php
     *
     * @return int scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->pos;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid.
     *
     * @link http://php.net/manual/en/iterator.valid.php
     *
     * @return bool The return value will be casted to boolean and then evaluated.
     *              Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->pos < $this->count();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element.
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind()
    {
        rewind($this->handle);
        $this->pos = 0;
    }
}
