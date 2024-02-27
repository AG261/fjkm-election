<?php

namespace App\Services\Common;


class Utils
{

    public function __construct(
        
    ){
    }

    /**
     * Array search like
     *
     * @param array $_array
     * @param string $_patron
     * @return array
     */
    public function arraySearchLike(array $_array, string $_patron): array
    {
        return array_filter($_array, static function (mixed $value) use ($_patron): bool {
            return 1 === preg_match(sprintf('/^%s$/i', preg_replace('/(^%)|(%$)/', '.*', $_patron)), $value);
        });
    }
}