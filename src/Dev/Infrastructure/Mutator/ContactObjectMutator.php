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

namespace Apparat\Dev\Infrastructure\Mutator;

use Apparat\Dev\Application\Utility\Random;
use Apparat\Object\Domain\Model\Object\ObjectInterface;

/**
 * Contact object mutator
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Infrastructure
 */
class ContactObjectMutator extends AbstractObjectMutator
{
    /**
     * Initialize the article
     *
     * @param ObjectInterface $object Article
     * @return ObjectInterface Article
     */
    public function initialize(ObjectInterface $object)
    {
        $this->setTitle($object); // p-name
        $this->setRandomDescription($object, .7); // p-summary
        $this->setRandomAbstract($object, .2);
        $this->setRandomCategories($object, .4); // p-category
        $this->setRandomKeywords($object, .7);
        $this->setAuthors($object); // TODO p-author

        // URL = u-url
        // URL = u-uid
        // system.published = dt-published
        // system.modified = dt-updated

        // p-location
        // u-syndication
        // u-in-reply-to
        // p-rsvp
        // p-comment
        // u-like-of
        // u-like
        // u-repost-of
        // u-repost (+ h-cite)
        // u-photo
        // u-audio
        // u-video
        // u-featured

        return $object;
    }

    /**
     * Mutate the article
     *
     * @param ObjectInterface $object Article
     * @return ObjectInterface Article
     */
    public function mutate(ObjectInterface $object)
    {
        $object->setPayload(Random::markdown()); // e-content

        $this->setRandomTitle($object, .1);
        $this->setRandomDescription($object, .2); // p-summary
        $this->setRandomAbstract($object, .1);
        $this->setRandomCategories($object, .2); // p-category
        $this->setRandomKeywords($object, .2);
//        $this->setAuthors($object); // TODO p-author

        return $object;
    }

    /**
     * Set the article title
     *
     * @param ObjectInterface $object Article
     * @return ObjectInterface $object Article
     */
    protected function setTitle(ObjectInterface $object)
    {
        return $object->setTitle($this->generator->text(70));
    }
    /**
     * Set the article description
     *
     * @param ObjectInterface $object Article
     * @return ObjectInterface $object Article
     */
    protected function setDescription(ObjectInterface $object)
    {
        return $object->setDescription($this->generator->text(200));
    }

    /**
     * Set the article abstract
     *
     * @param ObjectInterface $object Article
     * @return ObjectInterface $object Article
     */
    protected function setAbstract(ObjectInterface $object)
    {
        return $object->setAbstract($this->generator->realText(250));
    }

    /**
     * Set the article keywords
     *
     * @param ObjectInterface $object Article
     * @return ObjectInterface $object Article
     */
    protected function setKeywords(ObjectInterface $object)
    {
        return $object->setKeywords($this->generator->words(rand(0, 5)));
    }

    /**
     * Set the article categories
     *
     * @param ObjectInterface $object Article
     * @return ObjectInterface $object Article
     */
    protected function setCategories(ObjectInterface $object)
    {
        return $object->setCategories($this->generator->words(rand(0, 5)));
    }

    /**
     * Set the article authors
     *
     * @param ObjectInterface $object Article
     * @return ObjectInterface $object Article
     */
    protected function setAuthors(ObjectInterface $object)
    {
        return $object;
    }
}
