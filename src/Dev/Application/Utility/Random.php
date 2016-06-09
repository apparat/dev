<?php

/**
 * apparat-dev
 *
 * @category    Apparat
 * @package     Apparat\Dev
 * @subpackage  Apparat\Dev\Application
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

namespace Apparat\Dev\Application\Utility;

use Apparat\Kernel\Ports\Kernel;
use Faker\Factory;
use Faker\Generator;
use Faker\Provider\Biased;

/**
 * Random content generator
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Application
 */
class Random
{
    /**
     * Random markdown sample data
     *
     * @var array
     */
    protected static $markdown = null;
    /**
     * Faker instance
     *
     * @var Generator
     */
    protected static $faker = null;
    /**
     * Random image URL
     *
     * @var string
     */
    const RANDOM_IMAGE_URL = 'http://lorempixel.com/%s/%s/';

    /**
     * Return a markdown sample text
     *
     * @param boolean $images Inject images
     * @return string Markdown sample text
     * @see https://jaspervdj.be/lorem-markdownum
     */
    public static function markdown($images = true)
    {
        // One time loading of markdown sample data
        if (self::$markdown === null) {
            self::$markdown = require __DIR__.DIRECTORY_SEPARATOR.'Markdown.php';
            shuffle(self::$markdown);
        }

        $markdown = self::$markdown[array_rand(self::$markdown)];

        // If images should be injected
        if ($images) {
            $markdown = self::injectMarkdownImages($markdown);
        }

        return $markdown;
    }

    /**
     * Inject a random number of images (between 0 and 3) into a markdown source
     *
     * @param string $markdown Markdown
     * @return string Markdown with images
     */
    protected static function injectMarkdownImages($markdown)
    {
        $markdownBlocks = preg_split('%\R{2,}%', $markdown.PHP_EOL.PHP_EOL);

        // Iterate through a random number of images between 0 and 3
        for ($image = 0, $imageMax = 3 - self::faker()->biasedNumberBetween(0, 3); $image < $imageMax; ++$image) {
            $markdownBlocks[array_rand($markdownBlocks)] .= PHP_EOL.PHP_EOL.self::randomMarkdownImage();
        }

        return trim(implode(PHP_EOL.PHP_EOL, $markdownBlocks));
    }

    /**
     * Create and return a random image for Markdown use
     *
     * @return string Random image markdown
     */
    protected static function randomMarkdownImage()
    {
        return '!['.self::faker()->sentence().']('.sprintf(self::RANDOM_IMAGE_URL, rand(200, 400), rand(150, 300)).')';
    }

    /**
     * Create and return a Faker generator
     *
     * @return Generator Faker generator
     */
    protected static function faker()
    {
        if (self::$faker === null) {
            self::$faker = Factory::create();
            self::$faker->addProvider(Kernel::create(Biased::class));
        }
        return self::$faker;
    }
}
