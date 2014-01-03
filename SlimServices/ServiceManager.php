<?php
namespace SlimServices;

use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Slim\Slim;

class ServiceManager extends IlluminateContainer
{
	protected $app;
	protected $services = array();

	public function __construct(Slim $app)
	{
		$this->app = $app;

		$this->singleton('slim', function() use($app)
		{
			return $app;
		});

		$this->singleton('config', function($app)
		{
			return new Config($app['slim']);
		});

		$this['path'] = $app->config('path');

		$service_manager = $this;
		$app->hook('slim.before', function() use($service_manager)
		{
			$service_manager->boot();
		}, 1);		
	}

	/**
	 * Boot all registered service providers
	 */
	public function boot()
	{
		foreach ($this->services as $service) {
			$service->boot();
		}
	}

	/**
	 * Register a service provider with the application
	 */
	public function register(IlluminateServiceProvider $service)
	{
		$this->services[] = $service;

		$service->register();
	}

	/**
	 * Register services specified by class names in an array
	 */
	public function registerServices(array $services)
	{
		foreach ($services as $service) {
			$this->register(new $service($this));
		}
	}

	/**
	 * Overload the bind method so the services are added to the Slim DI container as well as Illuminate container
	 */
	public function bind($abstract, $concrete = null, $shared = false)
	{
		parent::bind($abstract, $concrete, $shared);

		$service_manager = $this;
		$this->app->$abstract = function() use($service_manager, $abstract)
		{
			return $service_manager->make($abstract);
		};
	}
}
