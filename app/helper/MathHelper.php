<?php

namespace app\helper;

/**
 * Ппредоставляет функции для точного сложения и вычитания чисел
 * с плавающей запятой, масштабируя их для избежания проблем с точностью.
 *
 * Вместо этого класса можно использовать специализированные библиотеки, такие как:
 * - BCMath (встроенная библиотека PHP): функции `bcadd` и `bcsub`
 * - GMP (GNU Multiple Precision)
 * Но ради 2 функций не стал их подключать, поэтому да, велосипед.
 */
class MathHelper
{
    /**
     * Точное сложение двух чисел с плавающей запятой.
     *
     * @param float $value1 Первое число.
     * @param float $value2 Второе число.
     * @return float Сумма чисел.
     */
    public static function add(float $value1, float $value2): float
    {
        $scaledValue1 = (int)($value1 * 10000000000000);
        $scaledValue2 = (int)($value2 * 10000000000000);

        $sumScaled = $scaledValue1 + $scaledValue2;

        return $sumScaled / 10000000000000;
    }

    /**
     * Точное вычитание двух чисел с плавающей запятой.
     *
     * @param float $value1 Уменьшаемое.
     * @param float $value2 Вычитаемое.
     * @return float Разность чисел.
     */
    public static function subtract(float $value1, float $value2): float
    {
        $scaledValue1 = (int)($value1 * 10000000000000);
        $scaledValue2 = (int)($value2 * 10000000000000);

        $differenceScaled = $scaledValue1 - $scaledValue2;

        return $differenceScaled / 10000000000000;
    }
}
