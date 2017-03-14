<?php

namespace Luna\Packager;

/**
 * Class ServiceProvider
 *
 * @package     Luna\Packager
 * @author      Thomas Wiringa <thomas.wiringa@gmail.com>
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Boot
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            MakePackageCommand::class
        ]);
    }
}
