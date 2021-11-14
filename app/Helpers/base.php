<?php

/**
 * @param int $st_num
 * @param int $end_num
 * @param int $mul
 * @return bool|float|int
 */
function rand_float(int $st_num = 0, int $end_num = 1, int $mul = 1000000)
{
    if ($st_num > $end_num) {
        return false;
    }
    return mt_rand($st_num * $mul, $end_num * $mul) / $mul;
}
