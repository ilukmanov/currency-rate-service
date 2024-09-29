<?php

namespace tests\helper;

use PHPUnit\Framework\TestCase;
use app\helper\MathHelper;

/**
 * Тесты для MathHelper.
 */
class MathHelperTest extends TestCase
{
    /**
     * Тест точного сложения двух чисел с плавающей запятой.
     *
     * @return void
     */
    public function testAdd(): void
    {
        $value1 = 0.1;
        $value2 = 0.2;
        $expectedSum = 0.3;

        $result = MathHelper::add($value1, $value2);

        $this->assertEqualsWithDelta($expectedSum, $result, 0.000000000001);
    }

    /**
     * Тест точного сложения с отрицательным числом.
     *
     * @return void
     */
    public function testAddWithNegativeValue(): void
    {
        $value1 = 1.5;
        $value2 = -0.5;
        $expectedSum = 1.0;

        $result = MathHelper::add($value1, $value2);

        $this->assertEqualsWithDelta($expectedSum, $result, 0.000000000001);
    }

    /**
     * Тест точного вычитания двух чисел с плавающей запятой.
     *
     * @return void
     */
    public function testSubtract(): void
    {
        $value1 = 0.3;
        $value2 = 0.1;
        $expectedDifference = 0.2;

        $result = MathHelper::subtract($value1, $value2);

        $this->assertEqualsWithDelta($expectedDifference, $result, 0.000000000001);
    }

    /**
     * Тест точного вычитания с отрицательным числом.
     *
     * @return void
     */
    public function testSubtractWithNegativeValue(): void
    {
        $value1 = 1.5;
        $value2 = -0.5;
        $expectedDifference = 2.0;

        $result = MathHelper::subtract($value1, $value2);

        $this->assertEqualsWithDelta($expectedDifference, $result, 0.000000000001);
    }

    /**
     * Тест сложения с очень маленькими значениями.
     *
     * @return void
     */
    public function testAddWithSmallValues(): void
    {
        $value1 = 0.000000000001;
        $value2 = 0.000000000002;
        $expectedSum = 0.000000000003;

        $result = MathHelper::add($value1, $value2);

        $this->assertEqualsWithDelta($expectedSum, $result, 0.000000000001);
    }

    /**
     * Тест вычитания с очень маленькими значениями.
     *
     * @return void
     */
    public function testSubtractWithSmallValues(): void
    {
        $value1 = 0.000000000003;
        $value2 = 0.000000000001;
        $expectedDifference = 0.000000000002;

        $result = MathHelper::subtract($value1, $value2);

        $this->assertEqualsWithDelta($expectedDifference, $result, 0.000000000001);
    }
}
