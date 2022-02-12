<?php

namespace Meletisf\Settings\Tests\Fixtures\Enums;

use Meletisf\Settings\Enums\Arrayable;

enum TestEnum: string {
    use Arrayable;

    case FirstValue = 'first';
    case SecondValue = 'second';
}
