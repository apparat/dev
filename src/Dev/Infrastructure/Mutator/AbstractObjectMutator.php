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

use Apparat\Dev\Ports\RuntimeException;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Ports\Types\Relation;
use Faker\Generator;

/**
 * Abstract object mutator
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Infrastructure
 * @method ObjectInterface setRandomTitle(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomDescription(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomAbstract(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomCategories(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomKeywords(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomLocation(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomSyndication(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomFeatured(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomAdditionalAuthor(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomAdditionalSyndication(ObjectInterface $object, $probability)
 */
abstract class AbstractObjectMutator implements ObjectMutatorInterface
{
    /**
     * Twitter status URL
     *
     * @var string
     */
    const TWITTER_STATUS = 'https://twitter.com/%s/status/%s';
    /**
     * Facebook post URL
     *
     * @var string
     */
    const FACEBOOK_POST = 'https://www.facebook.com/%s/posts/%s';
    /**
     * Google+ post URL
     *
     * @var string
     */
    const GOOGLEPLUS_POST = 'https://plus.google.com/+%s/posts/%s';
    /**
     * Instagram post URL
     *
     * @var string
     */
    const INSTAGRAM_POST = 'https://www.instagram.com/p/%s/';
    /**
     * Fake data generator
     *
     * @var Generator
     */
    protected $generator;

    /**
     * Abstract object mutator constructor
     *
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Magic setter for randomization of setter calls
     *
     * @param string $method Method name
     * @param array $arguments Arguments
     * @return ObjectInterface Object
     * @throws RuntimeException If the randomized setter is invalid
     */
    public function __call($method, array $arguments)
    {
        // If the randomized setter is invalid
        $setterMethod = 'set'.substr($method, 9);
        if (strncmp('setRandom', $method, 9) || !is_callable([$this, $setterMethod])) {
            throw new RuntimeException(
                sprintf('Invalid randomized setter "%s"', $setterMethod),
                RuntimeException::INVALID_RANDOMIZED_SETTER
            );
        }

        // If no object is given
        if (!count($arguments) || !($arguments[0] instanceof ObjectInterface)) {
            throw new RuntimeException('Invalid mutator subject', RuntimeException::INVALID_MUTATOR_SUBJECT);
        }

        // If the setter call should be randomized
        if ((count($arguments) > 1) && !$this->happens($arguments[1])) {
            return $arguments[0];
        }

        return $this->$setterMethod(...$arguments);
    }

    /**
     * Create a random signal with a particular probability
     *
     * @param float $probability Probability
     * @return bool Signal
     */
    protected function happens($probability = 0.0)
    {
        return rand(0, 100) <= (max(0, min(1, floatval($probability))) * 100);
    }

    /**
     * Set the object title
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface $object Object
     */
    protected function setTitle(ObjectInterface $object)
    {
        return $object->setTitle($this->generator->text(70));
    }

    /**
     * Set the object title
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface $object Object
     */
    protected function setLocation(ObjectInterface $object)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $object = $object->setLatitude($this->generator->latitude());
        /** @noinspection PhpUndefinedMethodInspection */
        $object = $object->setLongitude($this->generator->longitude());
        /** @var $object ObjectInterface */
        return $this->happens(.3) ? $object->setElevation($this->generator->randomFloat(5, -10, 1000)) : $object;
    }

    /**
     * Set the object description
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface $object Object
     */
    protected function setDescription(ObjectInterface $object)
    {
        return $object->setDescription($this->generator->text(200));
    }

    /**
     * Set the object abstract
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface $object Object
     */
    protected function setAbstract(ObjectInterface $object)
    {
        return $object->setAbstract($this->generator->realText(250));
    }

    /**
     * Set the object keywords
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface $object Object
     */
    protected function setKeywords(ObjectInterface $object)
    {
        return $object->setKeywords($this->generator->words(rand(0, 5)));
    }

    /**
     * Set the object categories
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface $object Object
     */
    protected function setCategories(ObjectInterface $object)
    {
        return $object->setCategories($this->generator->words(rand(0, 5)));
    }

    /**
     * Set the object authors
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface $object Object
     */
    protected function setAuthors(ObjectInterface $object)
    {
        // Run through a random number of authors between 1 and 2
        for ($author = 0, $maxAuthors = rand(1, 2); $author < $maxAuthors; ++$author) {
            $this->setAdditionalAuthor($object);
        }

        return $object;
    }

    /**
     * Add an additional author to the object
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface
     */
    protected function setAdditionalAuthor(ObjectInterface $object)
    {
        $url = $this->happens(.3) ? $this->generator->url : '';
        $email = $this->happens(.7) ? $this->generator->email : '';
        $label = $this->happens(.5) ? $this->generator->name : '';
        $author = $this->formatRelation($url, $email, $label);
        if (strlen($author)) {
            $object->addRelation($author, Relation::CONTRIBUTED_BY);
        }

        return $object;
    }

    /**
     * Format a relation string
     *
     * @param string $url URL
     * @param string $email Email address
     * @param string $label Label
     * @return string Relation string
     */
    protected function formatRelation($url = '', $email = '', $label = '')
    {
        return implode(' ', array_filter([trim($url), trim($email) ? "<$email>" : '', trim($label)]));
    }

    /**
     * Set the object syndication URLs
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface $object Object
     */
    protected function setSyndication(ObjectInterface $object)
    {
        // Run through a random number of syndication URLs between 1 and 3
        for ($syndication = 0, $maxSyndication = rand(1, 2); $syndication < $maxSyndication; ++$syndication) {
            $this->setAdditionalSyndication($object);
        }

        return $object;
    }

    /**
     * Add an additional syndication URL to the object
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface $object Object
     */
    protected function setAdditionalSyndication(ObjectInterface $object)
    {
        $syndicationUrls = [
            sprintf(self::TWITTER_STATUS, $this->generator->userName, $this->generator->creditCardNumber),
            sprintf(self::FACEBOOK_POST, $this->generator->userName, $this->generator->creditCardNumber),
            sprintf(
                self::GOOGLEPLUS_POST,
                $this->generator->userName,
                substr(base64_encode($this->generator->md5), 0, 12)
            ),
            sprintf(self::INSTAGRAM_POST, substr(base64_encode($this->generator->md5), 0, 12))
        ];

        return $object->addRelation($this->generator->randomElement($syndicationUrls), Relation::SYNDICATED_TO);
    }

    /**
     * Set an object feature image
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface $object Object
     */
    protected function setFeatured(ObjectInterface $object)
    {
        return $object->setDomain('featured', $this->generator->imageUrl(1024, 768));
    }
}
