<?php

namespace Tests\Unit;

use App\Rules\ValidCuil;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidCuilTest extends TestCase
{
    public function test_valid_cuil_passes_validation(): void
    {
        $validator = Validator::make(
            ['cuit' => '20-12345678-6'],
            ['cuit' => [new ValidCuil]]
        );

        // Compute valid check digit for 2012345678 -> 2*5+0*4+1*3+2*2+3*7+4*6+5*5+6*4+7*3+8*2 = 10+0+3+4+21+24+25+24+21+16 = 148; 148 % 11 = 5; 11 - 5 = 6.
        $this->assertFalse($validator->fails());
    }

    public function test_invalid_cuil_fails_validation(): void
    {
        $validator = Validator::make(
            ['cuit' => '20-12345678-9'],
            ['cuit' => [new ValidCuil]]
        );

        $this->assertTrue($validator->fails());
    }
}
