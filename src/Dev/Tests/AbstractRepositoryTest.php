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

/**
 * Abstract repository test
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Tests
 */
abstract class AbstractRepositoryTest extends AbstractTest
{
    /**
     * Generate and test a repository
     *
     * @param array $objectConfig Object configuration
     * @param int $expectedCount Expected number of objects
     * @param bool $emptyFiles Create empty files
     * @return RepositoryInterface Repository
     */
    protected function generateAndTestRepository(array $objectConfig, $expectedCount, $emptyFiles = false)
    {
        $tmpDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'apparat_test';
        $repository = Repository::generate(
            $this->registerTemporaryDirectory($tmpDirectory),
            $objectConfig,
            Repository::FLAG_CREATE_ROOT_DIRECTORY | ($emptyFiles ? Repository::FLAG_EMPTY_FILES : 0)
        );
        $datePath = str_repeat(DIRECTORY_SEPARATOR.'*', getenv('OBJECT_DATE_PRECISION'));
        $glob = $tmpDirectory.$datePath.DIRECTORY_SEPARATOR.'{.,}[!.,!..]*';
        $this->assertEquals($expectedCount, count(glob($glob, GLOB_ONLYDIR | GLOB_BRACE)));
        return $repository;
    }
}
