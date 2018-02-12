<?php
/**
 * Weave League DIC Adaptor.
 */
namespace Weave\Container\League;

use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
	/**
	 * Test calling the loadContainer method.
	 *
	 * Test the loadContainer method calls the provideContainerConfigs,
	 * passing in config and environment and returning a closure.
	 *
	 * @return null
	 */
	public function testContainerInstance()
	{
		$instance = $this->getMockBuilder(ContainerTestClass::class)
		->setMethods(['configureContainer'])
		->getMock();

		$instance->expects($this->once())
		->method('configureContainer')
		->with(
			$this->isInstanceOf(\League\Container\Container::class),
			$this->equalTo(['hello', 'world']),
			$this->equalTo('foo')
		);

		$instantiator = $instance->loadContainer(['hello', 'world'], 'foo');

		$this->assertInstanceOf(\Closure::class, $instantiator);
	}

	/**
	 * Test using the instantiator closeure and providers.
	 *
	 * Test the provided instantiator closure works and the
	 * pipeline and route provider closures work.
	 */
	public function testProviders()
	{
		$instance = new ContainerTestClass();

		$instantiator = $instance->loadContainer();
		$providerTestInstance = $instantiator(ContainerTestProviders::class);

		$this->assertInstanceOf(ContainerTestProviders::class, $providerTestInstance);
		$this->assertInstanceOf(\Closure::class, $providerTestInstance->pipelineProvider);
		$this->assertInstanceOf(\Closure::class, $providerTestInstance->routeProvider);

		$pipelineProvider = $providerTestInstance->pipelineProvider;
		$this->assertEquals('WibbleFoo', $pipelineProvider('Wibble'));

		$routeProvider = $providerTestInstance->routeProvider;
		$this->assertEquals('WibblePing', $routeProvider('Wibble'));
	}
}
