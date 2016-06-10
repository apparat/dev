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
use Apparat\Object\Ports\Types\Object;

/**
 * Contact object mutator
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Infrastructure
 * @method ObjectInterface setRandomEmail(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomLogo(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomPhoto(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomUrl(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomTel(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomNote(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomBday(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomKey(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomOrg(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomJobTitle(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomRole(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomImpp(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomSex(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomGenderIdentity(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomAnniversary(ObjectInterface $object, $probability)
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

        $this->happens(.4) ?
            $this->setRandomAdr($object, .8) : // p-adr - postal address, optionally embed an h-adr
            $this->initializeAddress($object);
        $this->happens(.5) ?
            $this->setRandomGeo($object, .8) : // u-geo
            $this->initializeLatitudeLongitudeAltitude($object);

        $this->setRandomTel($object, .5);
        $this->setRandomNote($object, .3);
        $this->setRandomBday($object, .3);

        $this->setRandomKey($object, .2);
        $this->setRandomOrg($object, .5);
        $this->setRandomJobTitle($object, .4);
        $this->setRandomRole($object, .4);

        $this->setRandomImpp($object, .1);
        $this->setRandomSex($object, .8);
        $this->setRandomGenderIdentity($object, .8);
        $this->setRandomAnniversary($object, .4);

        return $object;
    }

    /**
     * Initialize granular geo data
     *
     * @param ObjectInterface $object Object
     */
    protected function initializeLatitudeLongitudeAltitude(ObjectInterface $object)
    {
        if ($this->happens(.8)) {
            $this->setLatitude($object); // p-latitude - decimal latitude
            $this->setLongitude($object); // p-longitude - decimal longitude
            $this->setRandomAltitude($object, .5); // p-altitude - decimal altitude
        }
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

    /**
     * Set a telephone number
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setTel(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::TEL, '+'.$this->generator->randomNumber(8));
    }

    /**
     * Set an additional note
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setNote(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::NOTE, $this->generator->text());
    }

    /**
     * Set a birthday
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setBday(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::BDAY, $this->generator->dateTime->format('c'));
    }

    /**
     * Set a cryptographic key
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setKey(ObjectInterface $object)
    {
        return $object->setDomainProperty(
            ContactProperties::KEY,
            '0x'.substr($this->generator->creditCardNumber, 0, 8)
        );
    }

    /**
     * Set a birthday
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setOrg(ObjectInterface $object)
    {
        if ($this->happens(.5)) {
            return $object->setDomainProperty(ContactProperties::ORG, Random::apparatUrl(Object::CONTACT));
        }

        return $object->setDomainProperty(ContactProperties::ORG, $this->generator->company);
    }

    /**
     * Set a job title
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setJobTitle(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::JOB_TITLE, $this->generator->jobTitle);
    }

    /**
     * Set a role
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setRole(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::ROLE, $this->generator->jobTitle);
    }

    /**
     * Set an instant messager profiel
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setImpp(ObjectInterface $object)
    {
        $type = $this->generator->randomElement([
            'sip',
            'xmpp',
            'irc',
            'ymsgr',
            'msn',
            'aim',
            'im',
            'pres'
        ]);
        return $object->setDomainProperty(ContactProperties::IMPP, $type.':'.$this->generator->userName);
    }

    /**
     * Set a role
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setSex(ObjectInterface $object)
    {
        return $object->setDomainProperty(
            ContactProperties::SEX,
            $this->generator->randomElement(['M', 'F', 'O', 'N', 'U'])
        );
    }

    /**
     * Set a gender identity
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setGenderIdentity(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::GENDER_IDENTITY, $this->generator->words(3, true));
    }

    /**
     * Set a anniversary
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setAnniversary(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::ANNIVERSARY, $this->generator->dateTime->format('Y-m-d'));
    }
}
