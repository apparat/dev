<?php

/**
 * apparat-dev
 *
 * @category    Apparat
 * @package     Apparat\Dev
 * @subpackage  Apparat\Dev\Tests
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

use Apparat\Dev\Module;

/**
 * Basic tests for generic files
 *
 * @package     Apparat\Dev
 * @subpackage  Apparat\Dev\Tests
 */
abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Temporary files
     *
     * @var array
     */
    protected $tmpFiles = [];
    /**
     * Temporary directories
     *
     * @var array
     */
    protected $tmpDirectories = [];

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        Module::autorun();
    }

    /**
     * Tests if two arrays equal in their keys and values
     *
     * @param array $expected Expected result
     * @param array $actual Actual result
     * @param string $message Message
     */
    public function assertArrayEquals(array $expected, array $actual, $message = '')
    {
        $this->assertEquals(
            $this->sortArrayForComparison($expected),
            $this->sortArrayForComparison($actual),
            $message
        );
    }

    /**
     * Recursively sort an array for comparison with another array
     *
     * @param array $array Array
     * @return array                Sorted array
     */
    protected function sortArrayForComparison(array $array)
    {
        // Tests if all array keys are numeric
        $allNumeric = true;
        foreach (array_keys($array) as $key) {
            if (!is_numeric($key)) {
                $allNumeric = false;
                break;
            }
        }

        // If not all keys are numeric: Sort the array by key
        if (!$allNumeric) {
            ksort($array, SORT_STRING);
            return $this->sortArrayRecursive($array);
        }

        // Sort them by data type and value
        $array = $this->sortArrayRecursive($array);
        usort(
            $array,
            function (
                $first,
                $second
            ) {
                $aType = gettype($first);
                $bType = gettype($second);
                if ($aType === $bType) {
                    switch ($aType) {
                        case 'array':
                            return strcmp(implode('', array_keys($first)), implode('', array_keys($second)));
                        case 'object':
                            return strcmp(spl_object_hash($first), spl_object_hash($second));
                        default:
                            return strcmp(strval($first), strval($second));
                    }
                }

                return strcmp($aType, $bType);
            }
        );

        return $array;
    }

    /**
     * Recursively sort an array for comparison
     *
     * @param array $array Original array
     * @return array Sorted array
     */
    protected function sortArrayRecursive(array $array)
    {

        // Run through all elements and sort them recursively if they are an array
        reset($array);
        while (list($key, $value) = each($array)) {
            if (is_array($value)) {
                $array[$key] = $this->sortArrayForComparison($value);
            }
        }

        return $array;
    }

    /**
     * Tears down the fixture
     */
    protected function tearDown()
    {
        foreach ($this->tmpDirectories as $tmpDirectory) {
            $this->scanTemporaryDirectory($tmpDirectory);
        }
        foreach (array_reverse($this->tmpFiles) as $tmpFile) {
            @is_file($tmpFile) ? @unlink($tmpFile) : @rmdir($tmpFile);
        }
    }

    /**
     * Scan a temporary directory and register all files and subdirectories (recursively)
     *
     * @param string $directory Directory
     */
    protected function scanTemporaryDirectory($directory)
    {
        foreach (scandir($directory) as $fileOrDirectory) {
            if ($fileOrDirectory !== '.' && $fileOrDirectory !== '..' && !is_link($fileOrDirectory)) {
                $fileOrDirectory = $directory.DIRECTORY_SEPARATOR.$fileOrDirectory;
                $this->tmpFiles[] = $fileOrDirectory;
                if (is_dir($fileOrDirectory)) {
                    $this->scanTemporaryDirectory($fileOrDirectory);
                }
            }
        }
    }

    /**
     * Prepare and register a temporary file name
     *
     * @return string Temporary file name
     */
    protected function createTemporaryFileName()
    {
        $tempFileName = $this->createTemporaryFile();
        unlink($tempFileName);
        return $tempFileName;
    }

    /**
     * Prepare and register a temporary file
     *
     * @return string Temporary file name
     */
    protected function createTemporaryFile()
    {
        return $this->tmpFiles[] = tempnam(sys_get_temp_dir(), 'apparat_test_');
    }

    /**
     * Register a temporary directory that needs to be deleted recursively on shutdown
     *
     * @param string $directory Directory
     * @return string Directory
     */
    protected function registerTemporaryDirectory($directory)
    {
        return $this->tmpDirectories[] = $this->tmpFiles[] = $directory;
    }

    /**
     * Normalize HTML contents
     *
     * @param string $html Original HTML
     * @return string Normalized HTML
     */
    protected function normalizeHtml($html)
    {
        $htmlDom = new \DOMDocument();
        $htmlDom->preserveWhiteSpace = false;
        $htmlDom->formatOutput = false;
        $htmlDom->loadXML("<html><head><title>apparat</title></head><body>$html</body></html>");
        return $htmlDom->saveXML();
    }
}
