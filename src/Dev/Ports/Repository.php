<?php

/**
 * apparat-dev
 *
 * @category    Apparat
 * @package     Apparat\Dev
 * @subpackage  Apparat\Dev\Ports
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

namespace Apparat\Dev\Ports;

use Apparat\Dev\Application\Service\RepositoryGeneratorService;
use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Infrastructure\Repository\FileAdapterStrategy;
use Apparat\Object\Ports\Object;

/**
 * Repository tools
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Ports
 */
class Repository
{
    /**
     * Default object configuration
     *
     * @var array
     */
    protected static $defaultObjectConfig = [
        self::COUNT => 0,
        self::DRAFTS => 0,
        self::HIDDEN => 0,
        self::REVISIONS => 0,
    ];
    /**
     * Object count
     *
     * @var string
     */
    const COUNT = 'count';
    /**
     * Object drafts
     *
     * @var string
     */
    const DRAFTS = 'drafts';
    /**
     * Hidden objects
     *
     * @var string
     */
    const HIDDEN = 'hidden';
    /**
     * Max. string
     *
     * @var int
     */
    const REVISIONS = 'revisions';
    /**
     * Object type
     *
     * @var int
     */
    const TYPE = 'type';
    /**
     * Create empty files only
     *
     * @var int
     */
    const FLAG_EMPTY_FILES = 1;
    /**
     * Create root directory if non-existent
     *
     * @var int
     */
    const FLAG_CREATE_ROOT_DIRECTORY = 2;

    /**
     * Setup a dummy repository
     *
     * @param string $root Root directory
     * @param array $config Object configuration
     * @param int $flags Build flags
     */
    public static function generate($root, array $config, $flags = 0)
    {
        // If no valid root directory is given
        if (!is_string($root)
            || !strlen($root)
            || (!is_dir($root) && (($flags & self::FLAG_CREATE_ROOT_DIRECTORY) ? !mkdir($root, 0777, true) : true))
        ) {
            throw new InvalidArgumentsException(
                sprintf('Invalid root directory "%s"', $root),
                InvalidArgumentsException::INVALID_ROOT_DIRECTORY
            );
        }

        // Sanitize the object configuration
        $config = self::sanitizeConfigArray($config);

        // Build the repository
        self::generateValidatedParams($root, $config, $flags);
    }

    /**
     * Validate and sanitize the object configuration
     *
     * @param array $config Object configuration
     * @return array Object configuration
     */
    protected static function sanitizeConfigArray(array $config)
    {
        // Strip all invalid object types
        $config = array_intersect_key($config, Object::getSupportedTypes());

        // If the configuration is empty
        if (!count($config)) {
            throw new InvalidArgumentsException(
                'Empty object configuration',
                InvalidArgumentsException::EMPTY_OBJECT_CONFIG
            );
        }

        // Run through all object configurations
        foreach ($config as $objectType => $objectConfig) {
            $config[$objectType] = self::sanitizeObjectConfig($objectType, $objectConfig);
        }

        return $config;
    }

    /**
     * Validate and sanitize a single object configuration
     *
     * @param string $type Object type
     * @param array $config Single object configuration
     * @return array Single object configuration
     */
    protected static function sanitizeObjectConfig($type, array $config)
    {
        $config = array_merge(self::$defaultObjectConfig, $config);
        $config[self::COUNT] = intval($config[self::COUNT]);

        // If the number of objects is zero
        if ($config[self::COUNT] <= 0) {
            throw new InvalidArgumentsException(
                sprintf('Invalid object count "%s"', $config[self::COUNT]),
                InvalidArgumentsException::INVALID_OBJECT_COUNT
            );
        }

        // Sanitize values
        $config[self::DRAFTS] = max(0, min(1, floatval($config[self::DRAFTS])));
        $config[self::HIDDEN] = max(0, min(1, floatval($config[self::HIDDEN])));
        $config[self::REVISIONS] = intval($config[self::REVISIONS]);
        $config[self::TYPE] = $type;

        return $config;
    }

    /**
     * Setup a dummy repository with validated parameters
     *
     * @param string $root Root directory
     * @param array $config Object configuration
     * @param int $flags Build flags
     */
    protected static function generateValidatedParams($root, array $config, $flags)
    {
        // Prepare the repository
        $repository = \Apparat\Object\Ports\Repository::create(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => $root,
            ]
        );

        // Instantiate and run the repository generator service
        $repoBuilderType = ($flags & self::FLAG_EMPTY_FILES) ?
            '$ShallowRepositoryGeneratorService' : '$RepositoryGeneratorService';
        /** @var RepositoryGeneratorService $repoBuilder */
        $repoBuilder = Kernel::create($repoBuilderType, [$repository, $config]);
        $repoBuilder->generate($config);
    }
}
