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

namespace Apparat\Dev\Infrastructure\Factory;

/**
 * Container factory
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Infrastructure
 */
class ContainerFactory
{
    /**
     * Create a container path
     *
     * @param \DateTimeInterface $creationDate Object creation date and time
     * @param boolean $hidden Hidden object
     * @param int $uid Object ID
     * @param string $type Object type
     * @return string Container path
     */
    public function create(\DateTimeInterface $creationDate, $hidden, $uid, $type)
    {
        $components = array_slice(
            [
                $creationDate->format('Y'),
                $creationDate->format('m'),
                $creationDate->format('d'),
                $creationDate->format('H'),
                $creationDate->format('i'),
                $creationDate->format('s'),
            ],
            0,
            intval(getenv('OBJECT_DATE_PRECISION'))
        );
        $components[] = ($hidden ? '.' : '').$uid.'-'.$type;
        return implode('/', $components);
    }
}