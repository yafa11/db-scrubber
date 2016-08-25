<?php

namespace Scrubber;

use DateInterval;
use DateTime;
use DateTimeZone;

/**
 * Class ContentFiller
 */
class ContentFiller
{
    /** @var string  */
    private $firstNamePrefix = '';
    /** @var string  */
    private $firstNameSuffix = '';
    /** @var string  */
    private $surNamePrefix = '';
    /** @var string  */
    private $surNameSuffix = '';

    const CONSONANT = 1;
    const VOWEL = 2;

    /** @var array  */
    protected $vowels = array('a', 'e', 'i', 'o', 'u');
    /** @var array  */
    protected $consonants;
    /** @var array  */
    protected $charRange = array(97, 122);
    /** @var array  */
    protected $directions = array('N', 'S', 'E', 'W');
    /** @var array  */
    protected $streetTypes = array('LN', 'ST', 'CT', 'PKWY', 'DR', 'WAY', 'CIR');

    /**
     * @var array
     */
    private $surNames = array(
        'Smith',
        'Johnson',
        'Williams',
        'Brown',
        'Jones',
        'Miller',
        'Davis',
        'Garcia',
        'Rodriguez',
        'Wilson',
        'Martinez',
        'Anderson',
        'Taylor',
        'Thomas',
        'Hernandez',
        'Moore',
        'Martin',
        'Jackson',
        'Thompson',
        'White',
        'Lopez',
        'Lee',
        'Gonzalez',
        'Harris',
        'Clark',
        'Lewis',
        'Robinson',
        'Walker',
        'Perez',
        'Hall',
        'Young',
        'Allen',
        'Sanchez',
        'Wright',
        'King',
        'Scott',
        'Green',
        'Baker',
        'Adams',
        'Nelson',
        'Hill',
        'Ramirez',
        'Campbell',
        'Mitchell',
        'Roberts',
        'Carter',
        'Phillips',
        'Evans',
        'Turner',
        'Torres',
        'Parker',
        'Collins',
        'Edwards',
        'Stewart',
        'Flores',
        'Morris',
        'Nguyen',
        'Murphy',
        'Rivera',
        'Cook',
        'Rogers',
        'Morgan',
        'Peterson',
        'Cooper',
        'Reed',
        'Bailey',
        'Bell',
        'Gomez',
        'Kelly',
        'Howard',
        'Ward',
        'Cox',
        'Diaz',
        'Richardson',
        'Wood',
        'Watson',
        'Brooks',
        'Bennett',
        'Gray',
        'James',
        'Reyes',
        'Cruz',
        'Hughes',
        'Price',
        'Myers',
        'Long',
        'Foster',
        'Sanders',
        'Ross',
        'Morales',
        'Powell',
        'Sullivan',
        'Russell',
        'Ortiz',
        'Jenkins',
        'Gutierrez',
        'Perry',
        'Butler',
        'Barnes',
        'Fisher',
    );

    /**
     * @var array
     */
    private $maleNames = array(
        'Noah',
        'Liam',
        'Jacob',
        'Mason',
        'William',
        'Ethan',
        'Michael',
        'Alexander',
        'Jayden',
        'Daniel',
        'Elijah',
        'Aiden',
        'James',
        'Benjamin',
        'Matthew',
        'Jackson',
        'Logan',
        'David',
        'Anthony',
        'Joseph',
        'Joshua',
        'Andrew',
        'Lucas',
        'Gabriel',
        'Samuel',
        'Christopher',
        'John',
        'Dylan',
        'Isaac',
        'Ryan',
        'Nathan',
        'Carter',
        'Caleb',
        'Luke',
        'Christian',
        'Hunter',
        'Henry',
        'Owen',
        'Landon',
        'Jack',
        'Wyatt',
        'Jonathan',
        'Eli',
        'Isaiah',
        'Sebastian',
        'Jaxon',
        'Julian',
        'Brayden',
        'Gavin',
        'Levi',
        'Aaron',
        'Oliver',
        'Jordan',
        'Nicholas',
        'Evan',
        'Connor',
        'Charles',
        'Jeremiah',
        'Cameron',
        'Adrian',
        'Thomas',
        'Robert',
        'Tyler',
        'Colton',
        'Austin',
        'Jace',
        'Angel',
        'Dominic',
    );

    /**
     * @var array
     */
    private $femaleNames = array(
        'Sophia',
        'Emma',
        'Olivia',
        'Isabella',
        'Ava',
        'Mia',
        'Emily',
        'Abigail',
        'Madison',
        'Elizabeth',
        'Charlotte',
        'Avery',
        'Sofia',
        'Chloe',
        'Ella',
        'Harper',
        'Amelia',
        'Aubrey',
        'Addison',
        'Evelyn',
        'Natalie',
        'Grace',
        'Hannah',
        'Zoey',
        'Victoria',
        'Lillian',
        'Lily',
        'Brooklyn',
        'Samantha',
        'Layla',
        'Zoe',
        'Audrey',
        'Leah',
        'Allison',
        'Anna',
        'Aaliyah',
        'Savannah',
        'Gabriella',
        'Camila',
        'Aria',
        'Kaylee',
        'Scarlett',
        'Hailey',
        'Arianna',
        'Riley',
        'Alexis',
        'Nevaeh',
        'Sarah',
        'Claire',
        'Sadie',
        'Peyton',
        'Aubree',
        'Serenity',
        'Ariana',
        'Emmalyn',
        'Penelope',
        'Alyssa',
        'Bella',
        'Taylor',
        'Alexa',
        'Kylie',
        'Mackenzie',
        'Caroline',
        'Kennedy',
        'Autumn',
        'Lucy',
        'Ashley',
        'Madelyn',
    );

    /** @var int */
    private $maleNameCount;
    /** @var int */
    private $femaleNameCount;
    /** @var int */
    private $surNameCount;

    /**
     *
     */
    public function __construct()
    {
        $this->consonants = array();
        for ($i = 97; $i < 123; $i++) {
            $char = chr($i);
            if (!in_array($char, $this->vowels)) {
                $this->consonants[] = $char;
            }
        }

        $this->maleNameCount = count($this->maleNames) - 1;
        $this->femaleNameCount = count($this->femaleNames) - 1;
        $this->surNameCount = count($this->surNames) - 1;
    }


    /**
     * @param int $type
     * @return string
     */
    protected function getLetter($type = 0)
    {
        switch ($type) {
            case self::CONSONANT:
                $consonantIndex = mt_rand(1, count($this->consonants)) - 1;
                $char = $this->consonants[$consonantIndex];
                break;

            case self::VOWEL:
                $vowelIndex = mt_rand(1, count($this->vowels)) - 1;
                $char = $this->vowels[$vowelIndex];
                break;

            default:
                $charVal = mt_rand($this->charRange[0], $this->charRange[1]);
                $char = chr($charVal);
                break;
        }
        return $char;

    }


    /**
     * @param $minLength
     * @param $maxLength
     * @return string
     */
    protected function buildName($minLength, $maxLength)
    {
        $length = rand($minLength, $maxLength);
        $vowelCount = 0;
        $letterType = 0;
        $name = '';
        for ($i = 0; $i < $length; $i++) {
            if ($letterType == self::CONSONANT) {
                $letterType = self::VOWEL;
            } elseif ($letterType == self::VOWEL && $vowelCount >= 2) {
                $letterType = self::CONSONANT;
            } else {
                $letterType = rand(1, 2);
            }

            if ($letterType == self::VOWEL) {
                $vowelCount += 1;
            } else {
                $vowelCount = 0;
            }
            $name .= $this->getLetter($letterType);
        }

        return ucfirst($name);
    }


    /**
     * @param $minLength
     * @param $maxLength
     * @return int
     */
    private function determineLength($minLength, $maxLength)
    {
        $count = (int)$minLength;
        if (isset($maxLength) && (int)$maxLength > $count) {
            $count = mt_rand($count, $maxLength);
        }
        return $count;
    }


    /**
     * @param int $count
     * @return array
     */
    public function getSeveralFullNames($count = 3)
    {
        $names = array();
        for ($i = 0; $i < $count; $i++) {
            $names[] = $this->getFullName();
        }
        return $names;
    }


    /**
     * @param int $minLength
     * @param int $maxLength
     * @return string
     */
    public function getGeneratedName($minLength = 3, $maxLength = 7)
    {
        return $this->buildName($minLength, $maxLength);
    }


    /**
     * @return string
     */
    public function getFullName($gender = null)
    {
        $first = $this->getFirstName($gender);
        $last = $this->getSurName();
        return $first . ' ' . $last;
    }


    /**
     * @param null $gender
     * @return string
     */
    public function getFirstName($gender = null){
        switch(strtolower($gender)){
            case('m'):
                $name = $this->getMaleFirstName();
                break;
            case('f'):
                $name = $this->getFemaleFirstName();
                break;
            default:
                $rand = mt_rand(1,2);
                $name =  ($rand % 2) ? $this->getMaleFirstName() : $this->getFemaleFirstName();
                break;
        }
        return $this->firstNamePrefix.$name.$this->firstNameSuffix;
    }

    /**
     * @return string
     */
    protected function getMaleFirstName(){
        return $this->maleNames[mt_rand(0,$this->maleNameCount)];
    }

    /**
     * @return string
     */
    protected function getFemaleFirstName(){
        return $this->femaleNames[mt_rand(0,$this->femaleNameCount)];
    }

    /**
     * @return string
     */
    public function getSurName(){
        return $this->surNamePrefix.$this->surNames[mt_rand(0,$this->surNameCount)].$this->surNameSuffix;
    }

    /**
     * @param int $minLength
     * @param null $maxLength
     * @return string
     */
    public function getAlphaString($minLength = 10, $maxLength = null)
    {
        $count = $this->determineLength($minLength, $maxLength);

        $string = '';
        for ($i = 0; $i < $count; $i++) {
            $string .= $this->getLetter();
        }

        return $string;
    }


    /**
     * @param int $minLength
     * @param null $maxLength
     * @return string
     */
    public function getNumericString($minLength = 10, $maxLength = null)
    {
        $count = $this->determineLength($minLength, $maxLength);

        $number = '';
        for ($i = 0; $i < $count; $i++) {
            $number .= mt_rand(0, 9);
        }

        return $number;
    }


    /**
     * @param int $minLength
     * @param null $maxLength
     * @return string
     */
    public function getAlphaNumericString($minLength = 10, $maxLength = null)
    {
        $count = $this->determineLength($minLength, $maxLength);

        $alphaNumeric = '';
        for ($i = 0; $i < $count; $i++) {
            $type = mt_rand(0, 1);
            switch ($type) {
                case 0:
                    $alphaNumeric .= $this->getAlphaString(1);
                    break;
                case 1:
                    $alphaNumeric .= $this->getNumericString(1);
            }
        }
        return $alphaNumeric;
    }


    /**
     * @return string
     */
    public function getStreetAddress()
    {
        $address = $this->getNumericString(2, 6);
        $directionIndex = mt_rand(1, count($this->directions)) - 1;
        $address .= ' ' . $this->directions[$directionIndex] . '.';
        $address .= ' ' . $this->getName(3, 12);
        $streetTypeIndex = mt_rand(1, count($this->streetTypes)) - 1;
        $address .= ' ' . $this->streetTypes[$streetTypeIndex];

        return $address;
    }


    /**
     * @param int $nullPercent
     * @return null|string
     */
    public function getStreetAddress2($nullPercent = 85)
    {
        $rand = mt_rand(1, 100);
        if ($nullPercent < $rand) {
            $address2 = $this->getNumericString(3, 5);
            $address2 = ($rand % 2) ? '#' . $address2 : 'ste. ' . $address2;
            return $address2;
        }

        return null;
    }


    /**
     * @param boolean $full
     * @return string
     */
    public function getSex($full = 0)
    {
        $type = mt_rand(0, 1);
        if($type == 0){
            return ($full) ? 'Male' : 'M';
        }
        return ($full) ? 'Female' : 'F';
    }


    /**
     * @param $date
     * @param int $maxDaysToShift
     * @param string $format
     * @return string
     */
    public function shiftDateByRandomAmount($date, $maxDaysToShift = 365, $format = 'Y-m-d')
    {
        $randomDayShift = mt_rand(1, $maxDaysToShift);
        $direction = ($randomDayShift % 2) ? '+' : '-';

        $adjust = DateInterval::createfromdatestring($direction . $randomDayShift . ' day');
        $date = new DateTime($date, new DateTimeZone('UTC'));
        $date->add($adjust);
        return $date->format($format);
    }


    /**
     * @param null $uid
     * @return string
     */
    public function getUserName($uid = null)
    {
        $userName = $this->getName();
        if (isset($uid)) {
            $userName .= $uid;
        }
        return strtolower($userName);
    }


    /**
     * @param null $uid
     * @return string
     */
    public function getEmail($uid = null)
    {
        $email = $this->getUserName($uid) . '@' . $this->getName() . '.com';
        return strtolower($email);
    }


    /**
     * @param int $minWordCount
     * @param int $maxWordCount
     * @return string
     */
    public function getFreeText($minWordCount = 10, $maxWordCount = 60)
    {
        $wordCount = mt_rand($minWordCount, $maxWordCount);
        $string = '';
        for ($i = 0; $i < $wordCount; $i++) {
            $string .= ' ' . $this->getAlphaString(2, 10);
        }
        return $string;
    }

    /**
     * @param string $firstNamePrefix
     */
    public function setFirstNamePrefix($firstNamePrefix)
    {
        $this->firstNamePrefix = $firstNamePrefix;
    }

    /**
     * @param string $firstNameSuffix
     */
    public function setFirstNameSuffix($firstNameSuffix)
    {
        $this->firstNameSuffix = $firstNameSuffix;
    }

    /**
     * @param string $surNamePrefix
     */
    public function setSurNamePrefix($surNamePrefix)
    {
        $this->surNamePrefix = $surNamePrefix;
    }

    /**
     * @param string $surNameSuffix
     */
    public function setSurNameSuffix($surNameSuffix)
    {
        $this->surNameSuffix = $surNameSuffix;
    }

    /**
     * @param array $surNames
     */
    public function setSurNames($surNames)
    {
        $this->surNames = $surNames;
        $this->surNameCount = count($this->surNames) - 1;
    }

    /**
     * @param array $maleNames
     */
    public function setMaleNames($maleNames)
    {
        $this->maleNames = $maleNames;
        $this->maleNameCount = count($this->maleNames) - 1;
    }

    /**
     * @param array $femaleNames
     */
    public function setFemaleNames($femaleNames)
    {
        $this->femaleNames = $femaleNames;
        $this->femaleNameCount = count($this->femaleNames) - 1;
    }
}
