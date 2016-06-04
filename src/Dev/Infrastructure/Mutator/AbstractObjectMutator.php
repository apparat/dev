<?php

/**
 * apparat-dev
 *
 * @category    Apparat
 * @package     Apparat\Dev
 * @subpackage  Apparat\Dev\Infrastructure
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

namespace Apparat\Dev\Infrastructure\Mutator;

use Apparat\Dev\Ports\RuntimeException;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Faker\Generator;

/**
 * Abstract object mutator
 *
 * @package Apparat\Server
 * @subpackage Apparat\Dev\Infrastructure\Mutator
 */
abstract class AbstractObjectMutator implements ObjectMutatorInterface
{
    /**
     * Fake data generator
     *
     * @var Generator
     */
    protected $generator;

    /**
     * Abstract object mutator constructor
     *
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Magic setter for randomization of setter calls
     *
     * @param string $method Method name
     * @param array $arguments Arguments
     * @return ObjectInterface Object
     * @throws RuntimeException If the randomized setter is invalid
     */
    public function __call($method, array $arguments)
    {
        // If the randomized setter is invalid
        $setterMethod = 'set'.substr($method, 9);
        if (strncmp('setRandom', $method, 9) || !is_callable([$this, $setterMethod])) {
            throw new RuntimeException(
                sprintf('Invalid randomized setter "%s"', $setterMethod),
                RuntimeException::INVALID_RANDOMIZED_SETTER
            );
        }

        // If no object is given
        if (!count($arguments) || !($arguments[0] instanceof ObjectInterface)) {
            throw new RuntimeException('Invalid mutator subject', RuntimeException::INVALID_MUTATOR_SUBJECT);
        }

        // If the setter call should be randomized
        if ((count($arguments) > 1) && (rand(0, 100) > max(0, min(1, floatval($arguments[1]))) * 100)) {
            return $arguments[0];
        }

        return $this->$setterMethod($arguments[0]);
    }
}