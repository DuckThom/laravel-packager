<?php

namespace Luna\Packager;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

/**
 * Class MakePackageCommand
 *
 * @package     Luna\Packager
 * @author      Thomas Wiringa <thomas.wiringa@gmail.com>
 */
class MakePackageCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'make:package {Vendor} {Package} ' .
                            '{--base-dir=packages : Specify a different base dir for the packages}';

    /**
     * @var string
     */
    protected $description = "Create an empty package.";

    /**
     * @var string
     */
    protected $vendor;

    /**
     * @var string
     */
    protected $package;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $base;

    /**
     * Command handler
     *
     * @return int
     */
    protected function handle()
    {
        $this->base = $this->option('base-dir');
        $this->vendor = Str::camel($this->argument('Vendor'));
        $this->package = Str::camel($this->argument('Package'));
        $this->namespace = $this->vendor.'\\'.$this->package;

        if (!is_dir($this->getPackageDir())) {
            if (!mkdir($this->getPackageSrcDir(), 644, true)) {
                $this->error('Failed to create directory: ' . $this->getPackageSrcDir());

                return 1;
            }

            if (!mkdir($this->getPackageTestsDir(), 644, true)) {
                $this->error('Failed to create directory: ' . $this->getPackageTestsDir());

                return 1;
            }
        } else {
            if (!$this->confirm("The package's directory already exists, do you want to continue? ")) {
                return 1;
            }
        }

        $this->createServiceProvider();
        $this->createComposerFile();
        $this->createTestCaseFile();
    }

    /**
     * Get the package base directory
     *
     * @param  string  $file
     * @return string
     */
    public function getPackageDir($file = '')
    {
        return realpath($this->base.DIRECTORY_SEPARATOR.
            $this->vendor.DIRECTORY_SEPARATOR.
            $this->package.DIRECTORY_SEPARATOR.
            $file);
    }

    /**
     * Get the src dir inside the package dir
     *
     * @param  string  $file
     * @return string
     */
    public function getPackageSrcDir($file = '')
    {
        return realpath($this->getPackageDir().
            DIRECTORY_SEPARATOR.'src'.
            DIRECTORY_SEPARATOR.$file);
    }

    /**
     * Get the tests dir inside the package dir
     *
     * @param  string  $file
     * @return string
     */
    public function getPackageTestsDir($file = '')
    {
        return realpath($this->getPackageDir().
            DIRECTORY_SEPARATOR.'tests'.
            DIRECTORY_SEPARATOR.$file);
    }

    /**
     * Create the service provider class
     *
     * @return void
     */
    public function createServiceProvider()
    {
        $serviceProviderClassName = Str::camel($this->package.'ServiceProvider');
        $serviceProvider = file_get_contents(__DIR__.'../stubs/ServiceProvider.stub');
        $serviceProvider = $this->replaceVariables('{{namespace}}', $this->namespace, $serviceProvider);
        $serviceProvider = $this->replaceVariables('{{class}}', $serviceProviderClassName, $serviceProvider);

        file_put_contents(
            $this->getPackageSrcDir($serviceProviderClassName.'.php'),
            $serviceProvider
        );
    }

    /**
     * Create the composer file
     *
     * @return void
     */
    public function createComposerFile()
    {
        $composer = file_get_contents(__DIR__.'../stubs/composer.stub');
        $composer = $this->replaceVariable('{{package}}', strtolower($this->vendor.'/'.$this->package), $composer);
        $composer = $this->replaceVariable('{{namespace}}', $this->namespace.'\\', $composer);

        file_put_contents(
            $this->getPackageDir('composer.json'),
            $composer
        );
    }

    /**
     * Create the TestCase file
     *
     * @return void
     */
    public function createTestCaseFile()
    {
        $testCase = file_get_contents(__DIR__.'../stubs/TestCase.stub');

        file_put_contents(
            $this->getPackageTestsDir('composer.json'),
            $testCase
        );
    }

    /**
     * Replace a placeholder
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $input
     * @return string
     */
    public function replaceVariable($search, $replace, $input)
    {
        return str_replace($search, $replace, $input);
    }
}
