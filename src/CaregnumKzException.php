<?php
/**
 * Created by PhpStorm.
 * User: m.zharykov
 * Date: 28.04.2018
 * Time: 16:16
 */

namespace Malikzh;


class CaregnumKzException extends \Exception
{
    const CODE_INVALID_CARNUM = 0x01;
    const CODE_INVALID_REGION = 0x02;
}