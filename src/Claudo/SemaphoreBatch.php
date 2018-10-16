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
 * Class SemaphoreBatch
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class SemaphoreBatch
{
    /**
     * @var Semaphore[]
     */
    private $semaphores = [];

    public function add(Semaphore $semaphore)
    {
        $this->semaphores[] = $semaphore;
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public function synchronize(callable $callback)
    {
        while (true) {
            try {
                return $this->scan($callback);
            } catch (BusyQueueException $e) {
                usleep(500000);
            }
        }

        return null;
    }

    /**
     * @param callable $callback
     * @return mixed
     * @throws BusyQueueException
     */
    private function scan($callback)
    {
        foreach ($this->semaphores as $semaphore) {
            if ($semaphore->isLock()) {
                continue;
            }
            return $semaphore->synchronize($callback);
        }

        throw new BusyQueueException();
    }
}
