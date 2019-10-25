<?php

/*
 * This file is part of Underscore.php
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Underscore;

use StdClass;

class DispatchTest extends UnderscoreTestCase
{
    // Data providers ------------------------------------------------ /

    public function provideTypes()
    {
        return [
            ['string', 'Strings'],
            [5.14, 'Number'],
            [512, 'Number'],
            [1.2e3, 'Number'],
            [7E-10, 'Number'],
            [[], 'Arrays'],
            [new stdClass(), 'Objects'],
            [
                function () {
                    return;
                },
                'Functions',
            ],
            [null, 'Strings'],
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testCanGetClassFromType($subject, $expected)
    {
        $dispatch = Dispatch::toClass($subject);

        $this->assertEquals('Underscore\Types\\'.$expected, $dispatch);
    }

    public function testCanThrowExceptionAtUnknownTypes()
    {
        $this->setExpectedException('InvalidArgumentException');

        $file = fopen('../.travis.yml', 'w+');
        $dispatch = Dispatch::toClass($file);
    }
}
