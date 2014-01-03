<?php
namespace SlimServices;

use ArrayAccess;

use Slim\Slim;

/**
 * Simple wrapper around Slim's config method implementing array access
 */
class Config implements ArrayAccess
{
	protected $app;

	public function __construct(Slim $app)
	{
		$this->app = $app;
	}

	public function offsetExists($offset)
	{
		return (bool) $this->app->config($offset);
	}

	public function offsetGet($offset)
	{
		return $this->app->config($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->app->config($offset, $value);
	}

	public function offsetUnset($offset)
	{
		$this->app->config($offset, null);
	}
}
