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

namespace Apparat\Dev\Tests {

    use Apparat\Dev\Ports\Repository;
    use Apparat\Object\Ports\Types\Object;

    /**
     * Repository test
     *
     * @package Apparat\Dev
     * @subpackage Apparat\Dev\Tests
     */
    class RepositoryTest extends AbstractTest
    {
        /**
         * Tears down the fixture
         */
        public function tearDown()
        {
            putenv('MOCK_MKDIR');
            putenv('MOCK_TOUCH');
            parent::tearDown();
        }

        /**
         * Test the generation of a test repository
         *
         * @expectedException \Apparat\Dev\Ports\InvalidArgumentsException
         * @expectedExceptionCode 1464974472
         */
        public function testGenerateRepositoryInvalidRoot()
        {
            Repository::generate(null, []);
        }

        /**
         * Test the generation of a test repository with an empty object configuration
         *
         * @expectedException \Apparat\Dev\Ports\InvalidArgumentsException
         * @expectedExceptionCode 1464974779
         */
        public function testGenerateRepositoryInvalidObjectConfig()
        {
            Repository::generate($this->createTemporaryFileName(), [], Repository::FLAG_CREATE_ROOT_DIRECTORY);
        }

        /**
         * Test the generation of a test repository with zero object count
         *
         * @expectedException \Apparat\Dev\Ports\InvalidArgumentsException
         * @expectedExceptionCode 1464975382
         */
        public function testGenerateRepositoryEmptyObjectCount()
        {
            $objectConfig = [
                Object::ARTICLE => []
            ];
            Repository::generate(
                $this->createTemporaryFileName(),
                $objectConfig,
                Repository::FLAG_CREATE_ROOT_DIRECTORY
            );
        }

        /**
         * Test the generation of a test repository
         */
        public function testGenerateRepositoryEmptyFiles()
        {
            $objectConfig = [
                Object::ARTICLE => [
                    Repository::COUNT => 100,
                    Repository::HIDDEN => .2,
                    Repository::DRAFTS => .2,
                ],
                Object::EVENT => [
                    Repository::COUNT => 100,
                ],
                Object::CONTACT => [
                    Repository::COUNT => 100,
                    Repository::REVISIONS => -3,
                    Repository::DRAFTS => .2,
                ]
            ];
            $this->generateAndTestRepository($objectConfig, 200, true);
        }

        /**
         * Test the repository generation with failing mkdir()
         *
         * @expectedException \Apparat\Dev\Ports\RuntimeException
         * @expectedExceptionCode 1464997310
         */
        public function testGenerateRepositoryFailingMkdir()
        {
            putenv('MOCK_MKDIR=1');
            $this->testGenerateRepositoryEmptyFiles();
        }

        /**
         * Test the repository generation with failing touch()
         *
         * @expectedException \Apparat\Dev\Ports\RuntimeException
         * @expectedExceptionCode 1464997761
         */
        public function testGenerateRepositoryFailingTouch()
        {
            putenv('MOCK_TOUCH=1');
            $this->testGenerateRepositoryEmptyFiles();
        }

        /**
         * Test the generation of a test repository
         */
        public function testGenerateRepository()
        {
            $objectConfig = [
                Object::ARTICLE => [
                    Repository::COUNT => 10,
                    Repository::HIDDEN => .5,
                    Repository::DRAFTS => .2,
                    Repository::REVISIONS => -2,
                ],
            ];
            $this->generateAndTestRepository($objectConfig, 10, false);
        }

        /**
         * Generate and test a repository
         *
         * @param array $objectConfig Object configuration
         * @param int $expectedCount Expected number of objects
         * @param bool $emptyFiles Create empty files
         */
        protected function generateAndTestRepository(array $objectConfig, $expectedCount, $emptyFiles = false)
        {
            $tmpDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'apparat_test';
            Repository::generate(
                $this->registerTemporaryDirectory($tmpDirectory),
                $objectConfig,
                Repository::FLAG_CREATE_ROOT_DIRECTORY | ($emptyFiles ? Repository::FLAG_EMPTY_FILES : 0)
            );
            $datePath = str_repeat(DIRECTORY_SEPARATOR.'*', getenv('OBJECT_DATE_PRECISION'));
            $glob = $tmpDirectory.$datePath.DIRECTORY_SEPARATOR.'{.,}[!.,!..]*';
            $this->assertEquals($expectedCount, count(glob($glob, GLOB_ONLYDIR | GLOB_BRACE)));
        }
    }
}

namespace Apparat\Dev\Infrastructure\Factory {

    /**
     * Create a directory
     *
     * @param string $pathname Path name
     * @param int $mode File mode
     * @param bool $recursive Create parent directories
     * @return bool Success
     */
    function mkdir($pathname, $mode = 0777, $recursive = false)
    {
        return (getenv('MOCK_MKDIR') != 1) ? \mkdir($pathname, $mode, $recursive) : false;
    }

    /**
     * Sets access and modification time of file
     *
     * @param string $filename File name
     * @param null $time Modification time
     * @param null $atime Access time
     * @return bool Success
     */
    function touch($filename, $time = null, $atime = null)
    {
        return (getenv('MOCK_TOUCH') != 1) ? \touch($filename, $time, $atime) : false;
    }
}
