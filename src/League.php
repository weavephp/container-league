<?php
declare(strict_types = 1);
/**
 * Weave League DIC Adaptor.
 */
namespace Weave\Container\League;

use League\Container\Container;
/**
 * Weave League DIC Adaptor.
 */
trait League
{
	/**
	 * The Container instance.
	 *
	 * @var Container
	 */
	protected $_container;

	/**
	 * Setup the Dependency Injection Container
	 *
	 * @param array  $config      Optional config array as provided from _loadConfig().
	 * @param string $environment Optional indication of the runtime environment.
	 *
	 * @return callable A callable that can instantiate instances of classes from the DIC.
	 */
	protected function _loadContainer(array $config = [], $environment = null)
	{
		$this->_container = new Container;
		$this->_configureContainerInternal();
		$this->_configureContainer($this->_container, $config, $environment);
		return $this->_container->get('instantiator');
	}

	/**
	 * Configures the container.
	 *
	 * @param Container $container   The container.
	 * @param array     $config      Optional config array as provided from _loadConfig().
	 * @param string    $environment Optional indication of the runtime environment.
	 *
	 * @return null
	 */
	abstract protected function _configureContainer(Container $container, array $config = [], $environment = null);

	/**
	 * Configure's the internal Weave requirements.
	 *
	 * @return null
	 */
	protected function _configureContainerInternal()
	{
		$this->_container->add(
			'instantiator',
			function () { return function ($name) { return $this->_container->get($name); }; }
		);

		$this->_container->add(\Weave\Middleware\Middleware::class)
		->withArgument(\Weave\Middleware\MiddlewareAdaptorInterface::class)
		->withArgument(function ($pipelineName) { return $this->_provideMiddlewarePipeline($pipelineName); })
		->withArgument('instantiator')
		->withArgument(\Weave\Http\RequestFactoryInterface::class)
		->withArgument(\Weave\Http\ResponseFactoryInterface::class)
		->withArgument(\Weave\Http\ResponseEmitterInterface::class);

		$this->_container->add(\Weave\Middleware\Dispatch::class)
		->withArgument('instantiator');

		$this->_container->add(\Weave\Router\Router::class)
		->withArgument(\Weave\Router\RouterAdaptorInterface::class)
		->withArgument(function ($router) { return $this->_provideRouteConfiguration($router); })
		->withArgument('instantiator');
	}
}