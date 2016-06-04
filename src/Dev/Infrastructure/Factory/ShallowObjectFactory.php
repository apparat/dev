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

use Apparat\Dev\Ports\Repository;
use Apparat\Dev\Ports\RuntimeException;
use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Model\Path\RepositoryPathInterface;
use Apparat\Object\Infrastructure\Repository\FileAdapterStrategy;

/**
 * Shallow object factory
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Infrastructure
 */
class ShallowObjectFactory extends AbstractObjectFactory
{
    /**
     * Container factory
     *
     * @var ContainerFactory
     */
    protected $containerFactory;


    /**
     * Constructor
     *
     * @param ContainerFactory $containerFactory Container factory
     */
    public function __construct(ContainerFactory $containerFactory)
    {
        $this->containerFactory = $containerFactory;
    }

    /**
     * Create a repository object
     *
     * @param \DateTimeInterface $creationDate Creation date
     * @param int $objectId Object ID
     * @param array $config Object configuration
     * @throws RuntimeException If the resource container cannot be created
     * @throws RuntimeException If the shallow resource cannot be created
     */
    public function create(\DateTimeInterface $creationDate, $objectId, array $config)
    {
        // Build the container path
        $hidden = $config[Repository::HIDDEN] ? (rand(1, 5) == 5) : false;
        $containerPath = '/'.$this->containerFactory->create($creationDate, $hidden, $objectId,
                $config[Repository::TYPE]).'/';

        // Run through all object paths and create (empty) files
        /** @var FileAdapterStrategy $adapterStrategy */
        $adapterStrategy = $this->repository->getAdapterStrategy();
        foreach ($this->revisionPaths($objectId, $config, $containerPath) as $revisionPathIndex => $revisionPath) {
            $this->createObjectResource($revisionPathIndex, $revisionPath, $adapterStrategy);
        }
    }

    /**
     * Create a single object resource
     *
     * @param int $revisionPathIndex Revision path index
     * @param string $revisionPath Revision repository path
     * @param FileAdapterStrategy $adapterStrategy
     * @throws RuntimeException If the shallow resource cannot be created
     */
    protected function createObjectResource($revisionPathIndex, $revisionPath, FileAdapterStrategy $adapterStrategy)
    {
        /** @var RepositoryPathInterface $revRepositoryPath */
        $revRepositoryPath = Kernel::create(RepositoryPath::class, [$this->repository, $revisionPath]);
        $absRevisionPath = $adapterStrategy->getAbsoluteResourcePath($revRepositoryPath);

        // For the first revision: Create the container directory
        if (!$revisionPathIndex) {
            $this->createObjectResourceContainer(dirname($absRevisionPath));
        }

        // If the shallow resource cannot be created
        if (!touch($absRevisionPath)) {
            throw new RuntimeException(
                sprintf('Cannot create shallow resource "%s"', $absRevisionPath),
                RuntimeException::CANNOT_CREATE_SHALLOW_RESOURCE
            );
        }
    }

    /**
     * Create the object resource container
     *
     * @param string $absContainerPath Absolute object resource container path
     * @throws RuntimeException If the resource container cannot be created
     */
    protected function createObjectResourceContainer($absContainerPath)
    {
        // If the resource container cannot be created
        if (!is_dir($absContainerPath) && !mkdir($absContainerPath, 0777, true)) {
            throw new RuntimeException(
                sprintf('Cannot create container directory "%s"', $absContainerPath),
                RuntimeException::CANNOT_CREATE_CONTAINER_DIRECTORY
            );
        }
    }

    /**
     * Generate the list of repository relative revision paths
     *
     * @param int $objectId Object ID
     * @param array $config Object configuration
     * @param string $containerPath Repository relative container path
     * @return array Repository relative revision paths
     */
    protected function revisionPaths($objectId, array $config, $containerPath)
    {
        // Determine the amount of archive revisions to create
        $revisions = ($config[Repository::REVISIONS] < 0) ?
            rand(1, abs($config[Repository::REVISIONS]) + 1) : (1 + $config[Repository::REVISIONS]);

        // Determine the draft status
        $draft = intval($config[Repository::DRAFTS] ? (rand(1, 5) == 5) : 0);

        // Build the revision path list
        $revisionPaths = [];
        for ($revision = 1; $revision < $revisions - $draft; ++$revision) {
            $revisionPaths[] = $containerPath.$objectId.'-'.$revision.'.'.getenv('OBJECT_RESOURCE_EXTENSION');
        }
        if ($revision <= $revisions - $draft) {
            $revisionPaths[] = $containerPath.$objectId.'.'.getenv('OBJECT_RESOURCE_EXTENSION');
        }
        if ($draft) {
            $revisionPaths[] = $containerPath.($draft ? '.' : '').$objectId.'-'.$revisions.
                '.'.getenv('OBJECT_RESOURCE_EXTENSION');
        }

        return $revisionPaths;
    }
}