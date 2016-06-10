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

use Apparat\Dev\Application\Utility\Random;
use Apparat\Object\Application\Model\Properties\Domain\Contact as ContactProperties;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Ports\Types\Object;
use Faker\Generator;

/**
 * Address mutator trait
 *
 * @package Apparat\Dev
 * @subpackage Apparat\Dev\Infrastructure
 * @property Generator $generator
 * @method ObjectInterface setRandomAdr(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomPostOfficeBox(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomExtendedAddress(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomStreetAddress(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomLocality(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomRegion(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomPostalCode(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomCountryName(ObjectInterface $object, $probability)
 * @method ObjectInterface setRandomLabel(ObjectInterface $object, $probability)
 */
trait AddressMutatorTrait
{
    /**
     * Initialize a postal address
     *
     * @param ObjectInterface $object Object
     */
    protected function initializeAddress(ObjectInterface $object)
    {
        $this->setRandomPostOfficeBox($object, .2);
        $this->setRandomExtendedAddress($object, .4);
        $this->setRandomStreetAddress($object, .9);
        $this->setLocality($object);
        $this->setRandomRegion($object, .5);
        $this->setRandomPostalCode($object, .9);
        $this->setRandomCountryName($object, .7);
        $this->setRandomLabel($object, .5);
    }

    /**
     * Set an embedded apparat address
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setAdr(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::ADR, Random::apparatUrl(Object::ADDRESS));
    }

    /**
     * Set the post office box
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setPostOfficeBox(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::POST_OFFICE_BOX, $this->generator->postcode);
    }

    /**
     * Set the extended address
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setExtendedAddress(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::EXTENDED_ADDRESS, $this->generator->secondaryAddress);
    }

    /**
     * Set the street address
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setStreetAddress(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::STREET_ADDRESS, $this->generator->streetAddress);
    }

    /**
     * Set the locality
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setLocality(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::LOCALITY, $this->generator->city);
    }

    /**
     * Set the region
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setRegion(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::REGION, $this->generator->state);
    }

    /**
     * Set the postal code
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setPostalCode(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::POSTAL_CODE, $this->generator->postcode);
    }

    /**
     * Set the postal code
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setCountryName(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::COUNTRY_NAME, $this->generator->country);
    }

    /**
     * Set the label
     *
     * @param ObjectInterface $object Contact
     * @return ObjectInterface $object Contact
     */
    protected function setLabel(ObjectInterface $object)
    {
        return $object->setDomainProperty(ContactProperties::LABEL, $this->generator->address);
    }
}
