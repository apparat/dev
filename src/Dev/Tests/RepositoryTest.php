<?php

/**
 * apparat-dev
 *
 * @category    Apparat
 * @package     Apparat\Server
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
use Apparat\Object\Ports\Object;

/**
 * Repository test
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Tests
 */
class RepositoryTest extends AbstractTest
{
    /**
     * Test the generation of a test repository
     *
     * @expectedException \Apparat\Dev\Ports\InvalidArgumentsException
     * @expectedExceptionCode 1464974472
     */
    public function testRepositoryBuildInvalidRoot()
    {
        Repository::generate(null, []);
    }

    /**
     * Test the generation of a test repository with an empty object configuration
     *
     * @expectedException \Apparat\Dev\Ports\InvalidArgumentsException
     * @expectedExceptionCode 1464974779
     */
    public function testRepositoryBuildInvalidObjectConfig()
    {
        Repository::generate($this->createTemporaryFileName(), [], Repository::FLAG_CREATE_ROOT_DIRECTORY);
    }

    /**
     * Test the generation of a test repository with zero object count
     *
     * @expectedException \Apparat\Dev\Ports\InvalidArgumentsException
     * @expectedExceptionCode 1464975382
     */
    public function testRepositoryBuildEmptyObjectCount()
    {
        $objectConfig = [
            Object::ARTICLE => []
        ];
        Repository::generate($this->createTemporaryFileName(), $objectConfig, Repository::FLAG_CREATE_ROOT_DIRECTORY);
    }

    /**
     * Test the generation of a test repository
     */
    public function testRepositoryBuild()
    {
        $objectConfig = [
            Object::ARTICLE => [
                Repository::COUNT => 100,
                Repository::HIDDEN => true,
                Repository::DRAFTS => true,
            ],
            Object::EVENT => [
                Repository::COUNT => 100,
            ],
            Object::CONTACT => [
                Repository::COUNT => 100,
                Repository::REVISIONS => -3,
                Repository::DRAFTS => true,
            ]
        ];
        Repository::generate(
            sys_get_temp_dir().DIRECTORY_SEPARATOR.'apparat-test',
            $objectConfig,
            Repository::FLAG_CREATE_ROOT_DIRECTORY | Repository::FLAG_EMPTY_FILES
        );
    }
}