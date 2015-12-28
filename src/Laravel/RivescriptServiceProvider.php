<?php
namespace Vulcan\Rivescript\Laravel;

use Illuminate\Support\ServiceProvider;

class RivescriptServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
	 * @var bool
	 */
    protected $defer = false;

    /**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

    /**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('rivescript', function($app) {
            $parser = $app->make('Vulcan\Rivescript\Parser');

			return new \Vulcan\Rivescript\Rivescript($parser);
		});
	}

    /**
	 * Get the services provided by the provider.
	 *
	 * @return string
	 */
	public function provides()
	{
		return ['rivescript'];
	}
}
