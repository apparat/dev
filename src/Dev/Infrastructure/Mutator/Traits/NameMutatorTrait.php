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

namespace Apparat\Dev\Infrastructure\Mutator\Traits;

use Apparat\Object\Application\Model\Properties\Domain\Contact as ContactProperties;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Properties\InvalidArgumentException;
use Faker\Generator;

/**
 * Name mutator trait
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Infrastructure
 * @property Generator $generator
 * @method ObjectInterface setRandomHonorificPrefix(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomGivenName(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomAdditionalName(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomFamilyName(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomHonorificSuffix(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomNickname(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomSortString(ObjectInterface $object, $probability)
 */
trait NameMutatorTrait
{
    /**
     * Set the contact honorific prefix
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setHonorificPrefix(ObjectInterface $object)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $object->setDomain(ContactProperties::HONORIFIC_PREFIX, $this->generator->title());
    }

    /**
     * Set the contact given name
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setGivenName(ObjectInterface $object)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $object->setDomain(ContactProperties::GIVEN_NAME, $this->generator->firstName());
    }

    /**
     * Set the contact family name
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setFamilyName(ObjectInterface $object)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $object->setDomain(ContactProperties::FAMILY_NAME, $this->generator->lastName());
    }

    /**
     * Set the contact additional name
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setAdditionalName(ObjectInterface $object)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $object->setDomain(ContactProperties::ADDITIONAL_NAME, $this->generator->firstName());
    }

    /**
     * Set the contact nickname
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setNickname(ObjectInterface $object)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $object->setDomain(ContactProperties::NICKNAME, $this->generator->firstName());
    }

    /**
     * Set the contact honorific suffix
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setHonorificSuffix(ObjectInterface $object)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $object->setDomain(ContactProperties::HONORIFIC_SUFFIX, $this->generator->suffix());
    }

    /**
     * Set the contact sortstring
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setSortString(ObjectInterface $object)
    {
        $nameParts = [
            ContactProperties::NICKNAME => '',
            ContactProperties::FAMILY_NAME => '',
            ContactProperties::HONORIFIC_SUFFIX => '',
            ContactProperties::HONORIFIC_PREFIX => '',
            ContactProperties::GIVEN_NAME => '',
            ContactProperties::ADDITIONAL_NAME => '',
        ];
        foreach (array_keys($nameParts) as $property) {
            try {
                $nameParts[$property] = $object->getDomain($property);
            } catch (InvalidArgumentException $e) {
                continue;
            }
        }
        $sortStringPart1 = implode(
            ' ',
            array_filter([
                $nameParts[ContactProperties::FAMILY_NAME],
                $nameParts[ContactProperties::HONORIFIC_SUFFIX]
            ])
        );
        $sortStringPart2 = implode(
            ' ',
            array_filter([
                $nameParts[ContactProperties::HONORIFIC_PREFIX],
                $nameParts[ContactProperties::GIVEN_NAME],
                strlen($nameParts[ContactProperties::NICKNAME]) ?
                    '"'.$nameParts[ContactProperties::NICKNAME].'"' : '',
                $nameParts[ContactProperties::ADDITIONAL_NAME],
            ])
        );
        $sortString = ($sortStringPart1 ?: '---').', '.($sortStringPart2 ?: '---');
        return $object->setDomain(ContactProperties::SORT_STRING, trim($sortString, ' ,'));
    }
}
