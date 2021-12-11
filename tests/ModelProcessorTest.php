<?php

namespace Meletisf\Settings\Tests;

use Meletisf\Settings\ModelProcessor;

class ModelProcessorTest extends TestCase
{
    /** @test */
    public function serialization_returns_null_if_class_is_invalid()
    {
        $result = ModelProcessor::unserialize("Meletisf\Settings\Models\Doesnt\Exist:1");
        $this->assertNull($result);
    }

    /** @test */
    public function serialization_returns_null_if_id_is_invalid()
    {
        $result = ModelProcessor::unserialize("Meletisf\Settings\Models\Setting:1000000");
        $this->assertNull($result);
    }
}
