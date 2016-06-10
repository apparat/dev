<?php

/**
 * apparat-dev
 *
 * @category    Apparat
 * @package     Apparat\Dev
 * @subpackage  Apparat\Dev\Tests
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

namespace Apparat\Dev\Tests;

use Apparat\Dev\Ports\Repository;
use Apparat\Object\Domain\Repository\RepositoryInterface;
use Apparat\Object\Ports\Types\Object;

/**
 * Contact repository test
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Tests
 */
class ContactRepositoryTest extends AbstractRepositoryTest
{
    /**
     * Test the generation of a test repository
     */
    public function testGenerateRepository()
    {
        $objectConfig = [
            Object::CONTACT => [
                Repository::COUNT => 10,
                Repository::HIDDEN => .5,
                Repository::DRAFTS => .2,
                Repository::REVISIONS => -2,
            ],
        ];
        $repository = $this->generateAndTestRepository($objectConfig, 10, false);
        $this->assertInstanceOf(RepositoryInterface::class, $repository);
//        exit;
    }
}
