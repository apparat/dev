<?php

/**
 * apparat-dev
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Apparat\Dev\Application\Service
 * @author      Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

namespace Apparat\Dev\Application\Service;

use Apparat\Dev\Ports\Repository;

/**
 * Repository iterator
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Application
 */
class RepositoryIterator implements RepositoryIteratorInterface
{
    /**
     * Object configuration
     *
     * @var array
     */
    protected $config;
    /**
     * Object stac
     *
     * @var array
     */
    protected $objectStack = [];
    /**
     * Day pointer
     *
     * @var \DateTime
     */
    protected $dayPointer;

    /**
     * Constructor
     *
     * @param array $config Object configuration
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Return the current object config
     *
     * @return array Object config
     */
    public function current()
    {
        return $this->config[array_shift($this->objectStack)];
    }

    /**
     * Forward the day pointer by a random period between 0 and 259200 seconds (= 3 days)
     */
    public function next()
    {
        $this->dayPointer->add(new \DateInterval('PT'.rand(0, 259200).'S'));
    }

    /**
     * Return the day pointer
     *
     * @return \DateTime Day pointer
     */
    public function key()
    {
        return $this->dayPointer;
    }

    public function valid()
    {
        return count($this->objectStack) > 0;
    }

    /**
     * Rewind the iterator and rebuild the object stack
     */
    public function rewind()
    {
        // Recreate a random object stack
        $this->objectStack = [];
        foreach ($this->config as $objectType => $objectConfig) {
            $this->objectStack = array_pad(
                $this->objectStack,
                count($this->objectStack) + $objectConfig[Repository::COUNT],
                $objectType
            );
        }
        shuffle($this->objectStack);

        // Reset the day pointer
        $this->dayPointer = new \DateTime();
    }
}