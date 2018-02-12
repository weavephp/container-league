<?php
/**
 * Weave League DIC Adaptor.
 */
namespace Weave\Container\League;

/**
 * Test class instantiated in the unit tests.
 *
 * Used for testing the instantiator and also the provider closures.
 */
class ContainerTestProviders
{
	/**
	 * The pipeline provider closure.
	 *
	 * @var \Closure
	 */
	public $pipelineProvider;

	/**
	 * The route provider closure.
	 *
	 * @var \Closure
	 */
	public $routeProvider;

	/**
	 * Constructor.
	 *
	 * @param \Closure $pipelineProvider
	 * @param \Closure $routeProvider
	 */
	public function __construct($pipelineProvider, $routeProvider)
	{
		$this->pipelineProvider = $pipelineProvider;
		$this->routeProvider = $routeProvider;
	}
}
