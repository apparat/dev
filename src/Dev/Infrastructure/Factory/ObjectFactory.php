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

use Apparat\Object\Domain\Model\Path\RepositoryPathInterface;

/**
 * Object factory
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Infrastructure
 */
class ObjectFactory extends ShallowObjectFactory
{

    /**
     * Create the object revisions
     *
     * @param int $objectId Object ID
     * @param array $config Object configuration
     * @param string $containerPath Repository relative container path
     */
    protected function createObjectRevisions($objectId, array $config, $containerPath)
    {

        /** @var RepositoryPathInterface $revRepositoryPath */
//        $revRepositoryPath = Kernel::create(RepositoryPath::class, [$this->repository, $revisionPath]);
//        $absRevisionPath = $adapterStrategy->getAbsoluteResourcePath($revRepositoryPath);

//        \Apparat\Object\Application\Factory\ObjectFactory::createFromParams($revRepositoryPath);

//        echo $objectId.': '.$containerPath.PHP_EOL;
//        // Run through all object paths and create (empty) files
//        /** @var FileAdapterStrategy $adapterStrategy */
//        $adapterStrategy = $this->repository->getAdapterStrategy();
//        foreach ($this->revisionPaths($objectId, $config, $containerPath) as $revisionPathIndex => $revisionPath) {
//            $this->createObjectResource($revisionPathIndex, $revisionPath, $adapterStrategy);
//        }
    }
}
