<?php
/**
 * Created by PhpStorm.
 * User: m.zharykov
 * Date: 28.04.2018
 * Time: 14:35
 */

namespace Malikzh;


// 1993
// W 000 WWW
// W 000 WW

// 2012
// KZ000WWW00 000WWW00
// KZ000WW00 000WW00


class CaregnumKzResult
{
    const TYPE_UNKNOWN    = 0;
    const TYPE_2012       = 1;
    const TYPE_1993       = 2;

    public $carRegNum     = '';

    public $regionName    = '';
    public $region2012    = '';
    public $region1993    = '';
    public $regionNum     = -1;

    public $regnumType    = 0;
    public $regnumData    = [];
}