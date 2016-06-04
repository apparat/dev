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

namespace Apparat\Dev\Application\Service;

use Apparat\Dev\Application\Contract\ObjectFactoryInterface;
use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Repository\RepositoryInterface;

/**
 * Repository builder service
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Application
 */
class RepositoryGeneratorService
{
    /**
     * Object factory
     *
     * @var ObjectFactoryInterface
     */
    protected $objectFactory;

    /**
     * Constructor
     *
     * @param ObjectFactoryInterface $objectFactory Object factory
     * @param RepositoryInterface $repository Repository instance
     */
    public function __construct(ObjectFactoryInterface $objectFactory, RepositoryInterface $repository)
    {
        $this->objectFactory = $objectFactory;
        $this->objectFactory->setRepository($repository);
    }

    /**
     * Build the repository by configuration
     *
     * @param array $config Object configuration
     */
    public function generate(array $config)
    {
        // Instantiate a repository iterator
        /** @var RepositoryIterator $repositoryIterator */
        $repositoryIterator = Kernel::create(RepositoryIterator::class, [$config]);

        $objectId = 0;

        // Iterate through the object stack
        /**
         * @var int $creationDate
         * @var array $objectConfig
         */
        foreach ($repositoryIterator as $creationDate => $objectConfig) {
            $this->objectFactory->create(new \DateTimeImmutable('@'.$creationDate), ++$objectId, $objectConfig);
        }
    }
}
