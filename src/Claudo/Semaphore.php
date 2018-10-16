<?php
/**
 * Claudo: Semaphore PHP
 * Copyright (c) NewClass (http://newclass.pl)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the file LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) NewClass (http://newclass.pl)
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Claudo;

/**
 * Class Semaphore
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class Semaphore
{
    /**
     * @var resource
     */
    private $handler;
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $name;

    /**
     * Semaphore constructor.
     * @param string $name
     * @param null $cacheDir
     */
    public function __construct($name, $cacheDir = null)
    {
        $this->name = $name;
        if (null === $cacheDir) {
            $cacheDir = sys_get_temp_dir();
        }
        $path = $cacheDir.DIRECTORY_SEPARATOR.$name;
        if (!file_exists($path)) {
            file_put_contents($path, '{}');
            chmod($path, 0777);
        }

        $this->handler = fopen($path, 'r+');
        $this->path = $path;
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public function synchronize(callable $callback)
    {
        $this->lock();
        $result = null;
        try {
            $result = call_user_func_array($callback, [$this]);
            return $result;
        } finally {
            $this->unlock();
        }
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $size = filesize($this->path);
        if (0 === $size) {
            return $default;
        }
        fseek($this->handler, 0);
        $data = fread($this->handler, $size);
        $result = json_decode($data, true);

        if (!isset($result[$key])) {
            return $default;
        }

        return $result[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        $size = filesize($this->path);
        if (!$size) {
            return null; //FIXME throw exception?
        }
        fseek($this->handler, 0);
        $data = fread($this->handler, 9999);
        $result = json_decode($data, true);
        $result[$key] = $value;
        ftruncate($this->handler, 0);
        fseek($this->handler, 0);
        fwrite($this->handler, json_encode($result));
        return $this;
    }

    /**
     *
     */
    public function __destruct()
    {
        fclose($this->handler);
    }

    /**
     *
     */
    private function lock()
    {
        flock($this->handler, LOCK_EX);
    }

    /**
     *
     */
    private function unlock()
    {
        flock($this->handler, LOCK_UN);
    }

    /**
     * @return bool
     */
    public function isLock()
    {
        $lock = flock($this->handler, LOCK_EX | LOCK_NB);
        if ($lock) {
            flock($this->handler, LOCK_UN);
        }
        return !$lock;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}