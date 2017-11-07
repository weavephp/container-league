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
	use \Weave\Container\Container;

	/**
	 * The Container instance.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Setup the Dependency Injection Container
	 *
	 * @param array  $config      Optional config array as provided from loadConfig().
	 * @param string $environment Optional indication of the runtime environment.
	 *
	 * @return callable A callable that can instantiate instances of classes from the DIC.
	 */
	protected function loadContainer(array $config = [], $environment = null)
	{
		$this->container = new Container;
		$this->configureContainerInternal();
		$this->configureContainer($this->container, $config, $environment);
		return $this->container->get('instantiator');
	}

	/**
	 * Configures the container.
	 *
	 * @param Container $container   The container.
	 * @param array     $config      Optional config array as provided from loadConfig().
	 * @param string    $environment Optional indication of the runtime environment.
	 *
	 * @return null
	 */
	abstract protected function configureContainer(Container $container, array $config = [], $environment = null);

	/**
	 * Configure's the internal Weave requirements.
	 *
	 * @return null
	 */
	protected function configureContainerInternal()
	{
		$this->container->add(
			'instantiator',
			function () {
				return function ($name) {
					return $this->container->get($name);
				};
			}
		);

		$this->container->add(\Weave\Middleware\Middleware::class)
		->withArgument(\Weave\Middleware\MiddlewareAdaptorInterface::class)
		->withArgument(
			function ($pipelineName) {
				return $this->provideMiddlewarePipeline($pipelineName);
			}
		)
		->withArgument(\Weave\Resolve\ResolveAdaptorInterface::class)
		->withArgument(\Weave\Http\RequestFactoryInterface::class)
		->withArgument(\Weave\Http\ResponseFactoryInterface::class)
		->withArgument(\Weave\Http\ResponseEmitterInterface::class);

		$this->container->add(
			\Weave\Resolve\ResolveAdaptorInterface::class,
			\Weave\Resolve\Resolve::class
		)
		->withArgument('instantiator');

		$this->container->add(\Weave\Middleware\Dispatch::class)
		->withArgument(\Weave\Resolve\ResolveAdaptorInterface::class);

		$this->container->add(\Weave\Router\Router::class)
		->withArgument(\Weave\Router\RouterAdaptorInterface::class)
		->withArgument(
			function ($router) {
				return $this->provideRouteConfiguration($router);
			}
		)
		->withArgument(\Weave\Resolve\ResolveAdaptorInterface::class);
	}
}
