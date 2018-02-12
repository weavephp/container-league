<?php
/**
 * Weave League DIC Adaptor.
 */
namespace Weave\Container\League;
use League\Container\Container;

/**
 * Test class using the League trait so we can unit test the trait.
 *
 * The methods are setup to return values used in the unit testing.
 */
class ContainerTestClass
{
	use League {
		loadContainer as public;
	}

	protected function provideMiddlewarePipeline($pipelineName = null)
	{
		return $pipelineName . 'Foo';
	}

	protected function provideRouteConfiguration($router)
	{
		return $router . 'Ping';
	}

	protected function configureContainer(Container $container, array $config = [], $environment = null)
	{
		$container->add(ContainerTestProviders::class)
		->withArgument(
			function ($pipelineName) {
				return $this->provideMiddlewarePipeline($pipelineName);
			}
		)
		->withArgument(
			function ($router) {
				return $this->provideRouteConfiguration($router);
			}
		);
	}
}
