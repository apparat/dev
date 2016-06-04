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

use Apparat\Dev\Infrastructure\Mutator\ArticleObjectMutator;
use Apparat\Dev\Infrastructure\Mutator\ObjectMutatorInterface;
use Apparat\Dev\Ports\Repository;
use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Ports\Object;

/**
 * Object factory
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Infrastructure
 */
class ObjectFactory extends AbstractObjectFactory
{
    /**
     * Create a repository object
     *
     * @param \DateTimeInterface $creationDate Creation date
     * @param int $objectId Object ID
     * @param array $config Object configuration
     */
    public function create(\DateTimeInterface $creationDate, $objectId, array $config)
    {
        $objectTypeMethod = ucfirst($config[Repository::TYPE]);
        $objectMutator = null;

        /** @var ObjectInterface $object */
        $object = $this->{'create'.$objectTypeMethod}($creationDate, $objectMutator);

        // Create the object revisions
        $this->createObjectRevisions($object, $config, $objectMutator);

        // Eventually hide the object if necessary
        if (($config[Repository::HIDDEN] > 0) ? (rand(0, 100) < ($config[Repository::HIDDEN] * 100)) : false) {
            $object->delete()->persist();
        }
    }

    /**
     * Create the object revisions
     *
     * @param ObjectInterface $object Object
     * @param array $config Object configuration
     * @param ObjectMutatorInterface $objectMutator Object mutator
     */
    protected function createObjectRevisions(
        ObjectInterface $object,
        array $config,
        ObjectMutatorInterface $objectMutator
    ) {
        // Determine the amount of archive revisions to create
        $revisions = ($config[Repository::REVISIONS] < 0) ?
            rand(1, abs($config[Repository::REVISIONS]) + 1) : (1 + $config[Repository::REVISIONS]);

        // Determine the draft status
        $draft = (($config[Repository::DRAFTS] > 0) ? (rand(0, 100) < $config[Repository::DRAFTS] * 100) : 0);

        // Build the revision path list
        for ($revision = 1; $revision < $revisions - $draft; ++$revision) {
            $objectMutator->mutate($object)->publish()->persist();
        }
        if ($revision <= $revisions - $draft) {
            $objectMutator->mutate($object)->publish()->persist();
        }
        if ($draft) {
            $objectMutator->mutate($object)->persist();
        }
    }

    /**
     * Create an article object
     *
     * @param \DateTimeInterface $creationDate Creation date
     * @return ObjectInterface Article
     */
    protected function createArticle(\DateTimeInterface $creationDate, ObjectMutatorInterface &$objectMutator = null)
    {
        $objectMutator = Kernel::create(ArticleObjectMutator::class);
        return $this->repository->createObject(Object::ARTICLE, '', [], $creationDate);
    }
}
