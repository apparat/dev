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
use Apparat\Dev\Infrastructure\Mutator\Traits\AddressMutatorTrait;
use Apparat\Dev\Infrastructure\Mutator\Traits\NameMutatorTrait;
use Apparat\Object\Application\Model\Properties\Domain\Contact as ContactProperties;
use Apparat\Object\Domain\Model\Object\ObjectInterface;

/**
 * Contact object mutator
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Infrastructure
 * @method ObjectInterface setRandomEmail(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomLogo(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomPhoto(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomUrl(ObjectInterface $object, $probability)
 *

 */
class ContactObjectMutator extends AbstractObjectMutator
{
    /**
     * Use traits
     */
    use NameMutatorTrait, AddressMutatorTrait;

    /**
     * Initialize the contact
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface Contact
     */
    public function initialize(ObjectInterface $object)
    {
        $this->setRandomLocation($object, .6); // p-location
        $this->setRandomDescription($object, .2); // p-summary
        $this->setRandomCategories($object, .4); // p-category
        $this->setRandomKeywords($object, .7);
        $this->setAuthors($object);

        // p-name - full/formatted name of the person or organisation (automatically set via the various name facets)
        $this->setRandomHonorificPrefix($object, .3); // p-honorific-prefix - e.g. Mrs., Mr. or Dr.
        $this->setGivenName($object); // p-given-name - given (often first) name
        $this->setRandomNickname($object, .3); // p-nickname - nickname/alias/handle
        $this->setRandomAdditionalName($object, .2); // p-additional-name - other/middle name
        $this->setRandomFamilyName($object, .9);// p-family-name - family (often last) name
        $this->setRandomHonorificSuffix($object, .1); // p-honorific-suffix - e.g. Ph.D, Esq.
        $this->setSortString($object); // p-sort-string - string to sort by

        $this->setRandomEmail($object, .5); // u-email - email address
        $this->setRandomLogo($object, .2); // u-logo - a logo representing the person or organisation
        $this->setRandomPhoto($object, .4); // u-photo
        $this->setRandomUrl($object, .7); // u-url - home page

        // Initialize a postal address
        $this->happens(.4) ?
            $this->setRandomAdr($object, .8) : // p-adr - postal address, optionally embed an h-adr
            $this->initializeAddress($object);


        // p-geo or u-geo, optionally embed an h-geo
        // 	Main article: h-geo
        // p-latitude - decimal latitude
        // p-longitude - decimal longitude
        // p-altitude - decimal altitude
        // p-tel - telephone number
        // p-note - additional notes
        // dt-bday - birth date
        // u-key - cryptographic public key e.g. SSH or GPG
        // p-org - affiliated organization, optionally embed an h-card
        // p-job-title - job title, previously 'title' in hCard, disambiguated.
        // p-role - description of role
        // u-impp per RFC4770, new in vCard4 (RFC 6350)
        // p-sex - biological sex, new in vCard4 (RFC 6350)
        // p-gender-identity - gender identity, new in vCard4 (RFC 6350)
        // dt-anniversary

        return $object;
    }


    /**
     * Mutate the article
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface Contact
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
     * Set the contact email
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setEmail(ObjectInterface $object)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $object->setDomainProperty(ContactProperties::EMAIL, $this->generator->email());
    }

    /**
     * Set a logo
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setLogo(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::LOGO, $this->generator->imageUrl());
    }

    /**
     * Set a photo
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setPhoto(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::PHOTO, $this->generator->imageUrl());
    }

    /**
     * Set a URL
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setUrl(ObjectInterface $object)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $object->setDomainProperty(ContactProperties::URL, $this->generator->url());
    }
}
