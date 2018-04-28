<?php
namespace Malikzh;


class CaregnumKz
{
    const TYPE_1993_REGEX = '~^([a-z])(\\d{3})([a-z]{2,3})$~';
    const TYPE_2012_REGEX = '~^(?:kz)?(\\d{3})([a-z]{2,3})(\\d{2})$~';

    /**
     * Список регионов для номеров образца 1993 года
     *
     * Информация взята из: http://egov.kz/cms/ru/articles/grnz
     * ATTENTION! Обратите внимание, что в массивах $regions1993, $regions2012, $regionsNames
     * порядок сохраняется. Если нарушить порядок, то это может привести к неправильному сопоставлению регионов.
     * Также, должно соблюдаться условие (псевдокод): count($regions1993) == count($regions2012) == count($regionsNames)
     *
     * Вы можете создать наследующий класс и переопределить этот массив, т.к. используется позднее статическое связывание.
     *
     * @var array
     * @see http://egov.kz/cms/ru/articles/grnz
     */
    public static $regions1993   = [
        'Z',
        'A',
        'C',
        'D',
        'B',
        'E',
        'L',
        'H',
        'M',
        'P',
        'N',
        'R',
        'X',
        'S',
        'T',
        'F'
    ];

    /**
     * Список регионов для номеров образца 2012 года
     *
     * Информация взята из: http://egov.kz/cms/ru/articles/grnz
     * ATTENTION! Обратите внимание, что в массивах $regions1993, $regions2012, $regionsNames
     * порядок сохраняется. Если нарушить порядок, то это может привести к неправильному сопоставлению регионов.
     * Также, должно соблюдаться условие (псевдокод): count($regions1993) == count($regions2012) == count($regionsNames)
     *
     * Вы можете создать наследующий класс и переопределить этот массив, т.к. используется позднее статическое связывание.
     *
     * @var array
     * @see http://egov.kz/cms/ru/articles/grnz
     */
    public static $regions2012   = [
        '01',
        '02',
        '03',
        '04',
        '05',
        '06',
        '07',
        '08',
        '09',
        '10',
        '11',
        '12',
        '13',
        '14',
        '15',
        '16'
    ];

    /**
     * Список регионов для номеров образца 2012 года
     *
     * Информация взята из: http://egov.kz/cms/ru/articles/grnz
     * ATTENTION! Обратите внимание, что в массивах $regions1993, $regions2012, $regionsNames
     * порядок сохраняется. Если нарушить порядок, то это может привести к неправильному сопоставлению регионов.
     * Также, должно соблюдаться условие (псевдокод): count($regions1993) == count($regions2012) == count($regionsNames)
     *
     * Вы можете создать наследующий класс и переопределить этот массив, т.к. используется позднее статическое связывание.
     *
     * @var array
     * @see http://egov.kz/cms/ru/articles/grnz
     */
    public static $regionsNames  = [
        'город Астана',
        'город Алматы',
        'Акмолинская область',
        'Актюбинская область',
        'Алматинская область',
        'Атырауская область',
        'Западно-Казахстанская область',
        'Жамбылская область',
        'Карагандинская область',
        'Костанайская область',
        'Кызылординская область',
        'Мангистауская область',
        'Южно-Казахстанская область',
        'Павлодарская область',
        'Северо-Казахстанская область',
        'Восточно-Казахстанская область'
    ];

    /**
     * Разбирает гос.номер и возвращает ответ. В случае неверного гос.номера бросит исключение.
     *
     * @param $sRegnum Гос.номер
     * @return CaregnumKzResult Результат
     * @throws CaregnumKzException В случае неверного гос.номера и иных ошибок бросит это исключение
     */
    public static function parse($sRegnum) {
        $sRegnum = mb_strtolower($sRegnum);
        $sRegnum = preg_replace('~\\s+~u', '', $sRegnum);
        $sRegnum = static::replaceCyrillicChars($sRegnum);

        $oResult = new CaregnumKzResult();

        if (preg_match(static::TYPE_1993_REGEX, $sRegnum, $matches)) {

            /// Порядок индексов $macthes:
            /// 1 - регион (буква)
            /// 2 - гос.номер (3 цифры)
            /// 3 - гос.номер (2-3 буквы)

            $oResult->regnumType = CaregnumKzResult::TYPE_1993;
            $oResult->regnumData = array_map('strtoupper', [$matches[1], $matches[2], $matches[3]]);

            $a_sRegionInfo = static::getRegionInfo1993($matches[1]);
        }
        elseif (preg_match(static::TYPE_2012_REGEX, $sRegnum, $matches)) {

            /// Порядок индексов $matches:
            /// 1 - гос.номер (3 цифры)
            /// 2 - гос.номер (2 буквы)
            /// 3 - регион (2 цифры)

            $oResult->regnumType = CaregnumKzResult::TYPE_2012;
            $oResult->regnumData = array_map('strtoupper', [$matches[1], $matches[2], $matches[3]]);

            $a_sRegionInfo = static::getRegionInfo2012($matches[3]);
        } else {
            throw new CaregnumKzException('Invalid car number', CaregnumKzException::CODE_INVALID_CARNUM);
        }

        if (!$a_sRegionInfo) {
            throw new CaregnumKzException('Invalid region', CaregnumKzException::CODE_INVALID_REGION);
        }

        $oResult->carRegNum  = strtoupper(preg_replace('~^kz~iu', '', $sRegnum));
        $oResult->region1993 = $a_sRegionInfo['region1993'];
        $oResult->region2012 = $a_sRegionInfo['region2012'];
        $oResult->regionName = $a_sRegionInfo['regionName'];
        $oResult->regionNum  = $a_sRegionInfo['regionNum'];

        return $oResult;
    }

    /**
     * Замена похожих кириллических символов
     *
     * Метод заменяет похожие кириллические символы на их аналоги на латинице
     *
     * @param $input
     * @return string
     */
    protected static function replaceCyrillicChars($input) {
        $a_sReplaceMap = [
            'а' => 'a',
            'в' => 'b',
            'е' => 'e',
            'з' => '3',
            'к' => 'k',
            'м' => 'm',
            'н' => 'h',
            'о' => 'o',
            'р' => 'p',
            'с' => 'c',
            'т' => 't',
            'у' => 'y',
            'х' => 'x',
            'ш' => 'w',
            'ь' => 'b',
            'я' => 'r'
        ];

        return str_replace(array_keys($a_sReplaceMap), array_values($a_sReplaceMap), $input);
    }

    /**
     * Возвращает информацию о регионе на основе номера региона гос.номера формата 2012 года
     *
     * @param string $sRegionId Регион, о котором нужно найти информацию
     * @return array|bool Информацию о регионе, если не найдено, возвратит false
     */
    protected static function getRegionInfo2012($sRegion2012) {
        $iKey = array_search($sRegion2012, static::$regions2012, true);

        if ($iKey === false) return false;

        return [
            'region2012' => static::$regions2012[$iKey],
            'region1993' => strtoupper(static::$regions1993[$iKey]),
            'regionName' => static::$regionsNames[$iKey],
            'regionNum'  => $iKey
        ];
    }

    /**
     * Возвращает информацию о регионе на основе номера региона гос.номера формата 1993 года
     *
     * @param string $sRegionId Регион, о котором нужно найти информацию
     * @return array|bool Информацию о регионе, если не найдено, возвратит false
     */
    protected static function getRegionInfo1993($sRegion1993) {
        $iKey = array_search(strtoupper($sRegion1993), static::$regions1993, true);

        if ($iKey === false) return false;

        return static::getRegionInfo2012(static::$regions2012[$iKey]);
    }
}