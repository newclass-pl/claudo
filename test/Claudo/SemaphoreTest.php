<?php
/**
 * Claudo: Router PHP
 * Copyright (c) NewClass (http://newclass.pl)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the file LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) NewClass (http://newclass.pl)
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Test\Via;

use Claudo\Semaphore;

/**
 * Class SemaphoreTest
 * @package Test\Claudo
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class SemaphoreTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testSetGet()
    {

        $semaphore = new Semaphore('test1');

        $semaphore->set('value', 'data');

        $value = $semaphore->get('value');

        $this->assertEquals('data', $value);
    }
}
