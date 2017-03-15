<?php

namespace Luna\Packager;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

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
    protected $signature = 'make:package {Vendor} {Package} {--base-dir=packages : Specify a different base dir for the packages}';

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
    public function handle()
    {
        $this->base = $this->option('base-dir');
        $this->vendor = Str::studly($this->argument('Vendor'));
        $this->package = Str::studly($this->argument('Package'));
        $this->namespace = $this->vendor.'\\'.$this->package;

        if (!File::exists($this->getPackageDir())) {
            File::makeDirectory($this->getPackageSrcDir(), 0755, true);
            File::makeDirectory($this->getPackageTestsDir(), 0755, true);
        } else {
            if (!$this->confirm("The package's directory already exists, do you want to continue? ")) {
                return 1;
            }
        }

        $this->createServiceProvider();
        $this->createComposerFile();
        $this->createTestCaseFile();
        $this->createReadmeFile();

        $this->output->success("The package files have been created in: '" . $this->getPackageDir() . "'");
    }

    /**
     * Get the package base directory
     *
     * @param  string  $file
     * @return string
     */
    public function getPackageDir($file = '')
    {
        return base_path($this->base.DIRECTORY_SEPARATOR.
            $this->vendor.DIRECTORY_SEPARATOR.
            $this->package.($file ? DIRECTORY_SEPARATOR . $file : ''));
    }

    /**
     * Get the src dir inside the package dir
     *
     * @param  string  $file
     * @return string
     */
    public function getPackageSrcDir($file = '')
    {
        return $this->getPackageDir('src'.($file ? DIRECTORY_SEPARATOR.$file : ''));
    }

    /**
     * Get the tests dir inside the package dir
     *
     * @param  string  $file
     * @return string
     */
    public function getPackageTestsDir($file = '')
    {
        return $this->getPackageDir('tests'.($file ? DIRECTORY_SEPARATOR.$file : ''));
    }

    /**
     * Get the path to the stubs directory
     *
     * @param  string  $file
     * @return string
     */
    public function getStubsDir($file = '')
    {
        return realpath(
            __DIR__.DIRECTORY_SEPARATOR.
            '..'.DIRECTORY_SEPARATOR.
            'stubs'.($file ? DIRECTORY_SEPARATOR.$file : ''));
    }

    /**
     * Create the service provider class
     *
     * @return void
     */
    public function createServiceProvider()
    {
        $serviceProviderClassName = Str::studly($this->package.'ServiceProvider');
        $serviceProvider = File::get($this->getStubsDir('ServiceProvider.stub'));
        $serviceProvider = $this->replaceVariable('{{namespace}}', $this->namespace, $serviceProvider);
        $serviceProvider = $this->replaceVariable('{{class}}', $serviceProviderClassName, $serviceProvider);

        File::put(
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
        $composer = File::get($this->getStubsDir('composer.stub'));
        $composer = $this->replaceVariable('{{package}}', strtolower($this->vendor.'/'.$this->package), $composer);
        $composer = $this->replaceVariable('{{namespace}}', $this->vendor.'\\\\'.$this->package.'\\\\', $composer);

        File::put(
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
        $testCase = File::get($this->getStubsDir('TestCase.stub'));

        File::put(
            $this->getPackageTestsDir('composer.json'),
            $testCase
        );
    }

    /**
     * Create a README.md file
     *
     * @return void
     */
    public function createReadmeFile()
    {
        $readme = File::get($this->getStubsDir('readme.stub'));
        $readme = $this->replaceVariable("{{package}}", $this->package, $readme);

        File::put(
            $this->getPackageDir('README.md'),
            $readme
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
