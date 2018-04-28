<?php

use Malikzh\CaregnumKz;

/**
 * Class CaregnumKzTest
 *
 * Основное тестирование функции parse
 */
class CaregnumKzTest extends PHPUnit_Framework_TestCase
{
    const REGNUM1 = 'D914PCA';
    const REGNUM2 = 'А161ВСТ'; // кириллица
    const REGNUM3 = 'А  161  ВСТ'; // кириллица
    const REGNUM4 = 'A161BC';
    const REGNUM5 = 'KZ098PAS05';
    const REGNUM6 = '098АЕВ05'; // кириллица

    /**
     * Тестирование функции parse
     *
     * @throws \Malikzh\CaregnumKzException
     */
    public function testParse()
    {
        mb_internal_encoding('UTF-8');
        error_reporting(E_ALL);

        $i = 0;

        // тестирование номера образца 1993 года
        $oResult = CaregnumKz::parse(self::REGNUM1);
        $this->asserts1993($oResult, [
            'carRegnum'   => self::REGNUM1,
            'regionName'  => CaregnumKz::$regionsNames[$oResult->regionNum],
            'region2012'  => '04',
            'region1993'  => 'D',
            'regnumType'  => \Malikzh\CaregnumKzResult::TYPE_1993,
            'regnumData'  => [
                'D', '914', 'PCA'
            ]
        ], ++$i);

        // тестируем если буквы введены в нижнем регистре
        $oResult = CaregnumKz::parse(strtolower(self::REGNUM1));
        $this->asserts1993($oResult, [
            'carRegnum'   => self::REGNUM1,
            'regionName'  => CaregnumKz::$regionsNames[$oResult->regionNum],
            'region2012'  => '04',
            'region1993'  => 'D',
            'regnumType'  => \Malikzh\CaregnumKzResult::TYPE_1993,
            'regnumData'  => [
                'D', '914', 'PCA'
            ]
        ], ++$i);

        // тестируем если буквы введены в кириллице в верхнем регистре
        $oResult = CaregnumKz::parse(self::REGNUM2);
        $this->asserts1993($oResult, [
            'carRegnum'   => 'A161BCT',
            'regionName'  => CaregnumKz::$regionsNames[$oResult->regionNum],
            'region2012'  => '02',
            'region1993'  => 'A',
            'regnumType'  => \Malikzh\CaregnumKzResult::TYPE_1993,
            'regnumData'  => [
                'A', '161', 'BCT'
            ]
        ], ++$i);

        // тестируем если буквы введены в кириллице в нижнем регистре
        $oResult = CaregnumKz::parse(mb_strtolower(self::REGNUM2));
        $this->asserts1993($oResult, [
            'carRegnum'   => 'A161BCT',
            'regionName'  => CaregnumKz::$regionsNames[$oResult->regionNum],
            'region2012'  => '02',
            'region1993'  => 'A',
            'regnumType'  => \Malikzh\CaregnumKzResult::TYPE_1993,
            'regnumData'  => [
                'A', '161', 'BCT'
            ]
        ], ++$i);

        // тестируем если буквы введены в кириллице в нижнем регистре c пробелами
        $oResult = CaregnumKz::parse(mb_strtolower(self::REGNUM3));
        $this->asserts1993($oResult, [
            'carRegnum'   => 'A161BCT',
            'regionName'  => CaregnumKz::$regionsNames[$oResult->regionNum],
            'region2012'  => '02',
            'region1993'  => 'A',
            'regnumType'  => \Malikzh\CaregnumKzResult::TYPE_1993,
            'regnumData'  => [
                'A', '161', 'BCT'
            ]
        ], ++$i);

        // тестируем если буквы введены в кириллице в нижнем регистре c пробелами с 2 буквами
        $oResult = CaregnumKz::parse(mb_strtolower(self::REGNUM4));
        $this->asserts1993($oResult, [
            'carRegnum'   => 'A161BC',
            'regionName'  => CaregnumKz::$regionsNames[$oResult->regionNum],
            'region2012'  => '02',
            'region1993'  => 'A',
            'regnumType'  => \Malikzh\CaregnumKzResult::TYPE_1993,
            'regnumData'  => [
                'A', '161', 'BC'
            ]
        ], ++$i);

        // Проверка 2012
        $oResult = CaregnumKz::parse(self::REGNUM5);
        $this->asserts1993($oResult, [
            'carRegnum'   => '098PAS05',
            'regionName'  => CaregnumKz::$regionsNames[$oResult->regionNum],
            'region2012'  => '05',
            'region1993'  => 'B',
            'regnumType'  => \Malikzh\CaregnumKzResult::TYPE_2012,
            'regnumData'  => [
                '098', 'PAS', '05'
            ]
        ], ++$i);

        // Проверка 2012 - кириллица
        $oResult = CaregnumKz::parse(self::REGNUM6);
        $this->asserts1993($oResult, [
            'carRegnum'   => '098AEB05',
            'regionName'  => CaregnumKz::$regionsNames[$oResult->regionNum],
            'region2012'  => '05',
            'region1993'  => 'B',
            'regnumType'  => \Malikzh\CaregnumKzResult::TYPE_2012,
            'regnumData'  => [
                '098', 'AEB', '05'
            ]
        ], ++$i);

        $throwed = false;
        try {
            CaregnumKz::parse('invalid');
        } catch (\Malikzh\CaregnumKzException $e) {
            $throwed = true;
            $this->assertEquals($e->getCode(), \Malikzh\CaregnumKzException::CODE_INVALID_CARNUM);
        }
        $this->assertTrue($throwed, 'throw exception');

        $throwed = false;
        try {
            CaregnumKz::parse('kz098asd88');
        } catch (\Malikzh\CaregnumKzException $e) {
            $throwed = true;
            $this->assertEquals($e->getCode(), \Malikzh\CaregnumKzException::CODE_INVALID_REGION);
        }
        $this->assertTrue($throwed, 'throw exception');

        $throwed = false;
        try {
            CaregnumKz::parse('q098asd');
        } catch (\Malikzh\CaregnumKzException $e) {
            $throwed = true;
            $this->assertEquals($e->getCode(), \Malikzh\CaregnumKzException::CODE_INVALID_REGION);
        }
        $this->assertTrue($throwed, 'throw exception');
    }

    /**
     * Осуществляет проверку возврата
     *
     * @param $oResult
     * @param $expected
     * @param $num
     */
    public function asserts1993($oResult, $expected, $num) {
        $this->assertTrue($oResult instanceof \Malikzh\CaregnumKzResult, sprintf('Test #%d: Result must be instance of CaregnumResult', $num));
        $this->assertEquals($oResult->carRegNum, $expected['carRegnum'], sprintf('Test #%d: Check carRegnum', $num));
        $this->assertEquals($oResult->regionName, $expected['regionName'], sprintf('Test #%d: Check regionName', $num));
        $this->assertEquals($oResult->region2012, $expected['region2012'], sprintf('Test #%d: Check region2012', $num));
        $this->assertEquals($oResult->region1993, $expected['region1993'], sprintf('Test #%d: Check region1993', $num));
        $this->assertEquals($oResult->regnumType, $expected['regnumType'], sprintf('Test #%d: Check regnumType', $num));
        $this->assertEquals($oResult->regnumData[0], $expected['regnumData'][0], sprintf('Test #%d: Check regnumData[0]', $num));
        $this->assertEquals($oResult->regnumData[1], $expected['regnumData'][1], sprintf('Test #%d: Check regnumData[1]', $num));
        $this->assertEquals($oResult->regnumData[2], $expected['regnumData'][2], sprintf('Test #%d: Check regnumData[2]', $num));
    }
}
