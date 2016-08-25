<?php

use Scrubber\ContentFiller;

class ContentFillerTest extends PHPUnit_Framework_TestCase
{
    /** @var ContentFiller */
    private $sut;

    private $maleNames = ['maleName1', 'maleName2', 'maleName3'];
    private $femaleNames = ['femaleName1', 'femaleName2', 'femaleName3'];
    private $surNames = ['surName1', 'surName2','surName2'];

    public function setup(){
        $this->sut = new ContentFiller();
        $this->sut->setMaleNames($this->maleNames);
        $this->sut->setFemaleNames($this->femaleNames);
        $this->sut->setSurNames($this->surNames);
    }

    public function testGetSeveralFullNames(){
        $count = 5;
        $result = $this->sut->getSeveralFullNames($count);

        self::assertTrue(is_array($result));
        self::assertCount($count, $result);
    }

    public function testGetFullNames(){
        $result = $this->sut->getFullName();
        $resultArray = explode(' ',$result);
        self::assertCount(2, $resultArray);
        self::assertTrue(in_array($resultArray[0], $this->maleNames) || in_array($resultArray[0], $this->femaleNames));
        self::assertTrue(in_array($resultArray[1], $this->surNames));

        $result = $this->sut->getFullName('m');
        $resultArray = explode(' ',$result);
        self::assertCount(2, $resultArray);
        self::assertTrue(in_array($resultArray[0], $this->maleNames));
        self::assertTrue(in_array($resultArray[1], $this->surNames));

        $result = $this->sut->getFullName('f');
        $resultArray = explode(' ',$result);
        self::assertCount(2, $resultArray);
        self::assertTrue(in_array($resultArray[0], $this->femaleNames));
        self::assertTrue(in_array($resultArray[1], $this->surNames));
    }

    public function testGetGeneratedName(){
        $result = $this->sut->getGeneratedName($min = 2,$max = 15);
        self::assertTrue(strlen($result) >= $min && strlen($result) <= $max);

        $result = $this->sut->getGeneratedName($sameMinMax = 2,$sameMinMax);
        self::assertTrue(strlen($result) === $sameMinMax);
    }

    public function testGetFirstName(){
        $result = $this->sut->getFirstName('m');
        self::assertTrue(in_array($result, $this->maleNames));

        $result = $this->sut->getFirstName('f');
        self::assertTrue(in_array($result, $this->femaleNames));

        $result = $this->sut->getFirstName();
        self::assertTrue(in_array($result, array_merge($this->maleNames,$this->femaleNames)));
    }

    public function testGetFirstNameWithPrefixAndSuffix(){
        $this->sut->setFirstNamePrefix($prefix = '111_');
        $this->sut->setFirstNameSuffix($suffix = '_999');
        $result = $this->sut->getFirstName();
        self::assertTrue(0 === strpos($result, $prefix));
        self::assertTrue(strlen($result) - strlen($suffix) === strpos($result, $suffix));
        $strippedName = substr($result, strlen($prefix), -strlen($suffix));
        self::assertTrue(in_array($strippedName, array_merge($this->maleNames,$this->femaleNames)));
    }

    public function testGetSurName(){
        $result = $this->sut->getSurName();
        self::assertTrue(in_array($result, $this->surNames));
    }

    public function testGetSurNameWithPrefixAndSuffix(){
        $this->sut->setSurNamePrefix($prefix = 'aaa_');
        $this->sut->setSurNameSuffix($suffix = '_zzz');
        $result = $this->sut->getSurName();
        self::assertTrue(0 === strpos($result, $prefix));
        self::assertTrue(strlen($result) - strlen($suffix) === strpos($result, $suffix));
        $strippedName = substr($result, strlen($prefix), -strlen($suffix));
        self::assertTrue(in_array($strippedName, $this->surNames));
    }

    public function testGetAlphaString(){
        $result = $this->sut->getAlphaString($min = 15, $max = 20);
        $strlen = strlen( $result );
        self::assertTrue($strlen >= $min && $strlen <= $max);
        for( $i = 0; $i < $strlen; $i++ ) {
            $char = substr( $result, $i, 1 );
            self::assertFalse(is_int($char));
        }
    }

    public function testGetNumericString(){
        $result = $this->sut->getNumericString($min = 7, $max = 12);
        $strlen = strlen( $result );
        self::assertTrue($strlen >= $min && $strlen <= $max);
        for( $i = 0; $i < $strlen; $i++ ) {
            $char = substr( $result, $i, 1 );
            self::assertTrue((int)$char != 0 || $char === '0');
        }
    }

    public function testGetAlphaNumericString(){
        $result = $this->sut->getAlphaNumericString($min = 2, $max = 82);
        $strlen = strlen( $result );
        self::assertTrue($strlen >= $min && $strlen <= $max);
    }

    public function testGetSex(){
        $result = $this->sut->getSex();
        self::assertTrue(in_array($result, ['M','F']));

        $result = $this->sut->getSex(true);
        self::assertTrue(in_array($result, ['Male','Female']));
    }
}
