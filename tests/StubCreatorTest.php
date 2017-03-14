<?php

namespace Tests;

use Luna\Packager\MakePackageCommand;

/**
 * Class StubCreatorTest
 *
 * @package     Luna\Packager
 * @subpackage  Tests
 * @author      Thomas Wiringa <thomas.wiringa@gmail.com>
 */
class StubCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function replacesPlaceholder()
    {
        $command = new MakePackageCommand;
        $input = "{{placeholder}}";
        $output = "Blaat";
        $replaced = $command->replaceVariable(
            $input,
            $output,
            $input
        );

        $this->assertEquals($output, $replaced);
    }

    /**
     * @test
     */
    public function replacesPlaceholderFromStubInput()
    {
        $command = new MakePackageCommand;
        $replaced = $command->replaceVariable(
            "{{place}}",
            "world",
            file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'test.stub')
        );

        $this->assertEquals("Hello world!", $replaced);
    }
}
