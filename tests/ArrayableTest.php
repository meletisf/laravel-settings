<?php

namespace Meletisf\Settings\Tests;

use Meletisf\Settings\Tests\Fixtures\Enums\TestEnum;

class ArrayableTest extends TestCase
{
    /** @test */
    public function it_returns_enum_cases_as_array()
    {
        $cases = TestEnum::names();

        /* @see TestEnum */
        $this->assertIsArray($cases);
        $this->assertTrue(in_array('FirstValue', $cases));
        $this->assertTrue(in_array('SecondValue', $cases));
    }

    /** @test */
    public function it_returns_enum_values_as_array()
    {
        $values = TestEnum::values();

        /* @see TestEnum */
        $this->assertIsArray($values);
        $this->assertTrue(in_array('first', $values));
        $this->assertTrue(in_array('second', $values));
    }

    /** @test */
    public function it_returns_an_associative_array()
    {
        $array = TestEnum::toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('FirstValue', $array);
        $this->assertArrayHasKey('SecondValue', $array);
    }
}
