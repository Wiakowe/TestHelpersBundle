<?php
/**
 * ControllerTestCase.php
 *
 * File wich contains the ControllerTestCase class.
 *
 * @package ApiBundle
 * @subpackage Tests
 */

namespace Finday\TestHelpersBundle;

/**
 * Abstract class to ease up the creation of controller tests.
 *
 * @author  Roger Llopart Pla <roger@finday.com>
 *
 * @version Release:2.0.0.
 */
abstract class ControllerTestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * Mock of the container.
	 *
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;

	/**
	 * The controller under test.
	 *
	 * @var \Symfony\Bundle\FrameworkBundle\Controller\Controller
	 */
	protected $tested_controller;

	/**
	 * Array of calls to the get call of the container object.
	 *
	 * @var array
	 */
	protected $container_get_map;

	/**
	 * The culture of the session.
	 *
	 * @var string
	 */
	protected $culture;

	/**
	 * Mock of the request object.
	 *
	 * @var Symfony\Component\HttpFoundation\Request
	 */
	protected $request;

	/**
	 * Mock of the session object.
	 *
	 * @var Symfony\Component\HttpFoundation\Session
	 */
	protected $session;

	/**
	 * Mock of the templating object.
	 *
	 * @var Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
	 */
	protected $templating;

	/**
	 * Function wich initializes the attribute $tested_controller.
	 */
	public abstract function initializeController();

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp()
	{
		$this->initializeController();

		$this->culture = 'es_ES';

		$this->container = $this->getMockBuilder(
				'Symfony\Component\DependencyInjection\ContainerInterface'
		)
		->disableOriginalConstructor()
		->getMock();

		$this->container_get_map = array();

		// Wich are always required.
		$this->request = $this->getMockBuilder(
				'Symfony\Component\HttpFoundation\Request'
		)
		->disableOriginalConstructor()
		->getMock();

		$this->session = $this->getMockBuilder(
				'Symfony\Component\HttpFoundation\Session'
		)
		->disableOriginalConstructor()
		->getMock();

		$this->request->expects($this->any())
		->method('getSession')
		->will($this->returnValue($this->session));

		$this->session->expects($this->any())
		->method('getLocale')
		->will($this->returnValue($this->culture));

		$this->templating = $this->getMockBuilder(
						'Symfony\Bundle\FrameworkBundle\Templating\EngineInterface'
		)
		->disableOriginalConstructor()
		->getMock();

		$this->addToContainerMockGetMap('templating', $this->templating);

		$this->addToContainerMockGetMap('request', $this->request);
		$this->addToContainerMockGetMap('session', $this->session);

		$this->tested_controller->setContainer($this->container);
	}


	/**
	 * Adds an element with the given key to return the given value to the
	 * container.
	 *
	 * @param string $key   key
	 * @param mixed  $value value
	 */
	protected function addToContainerMockGetMap($key, $value)
	{
		$default_parameter =
		\Symfony\Component\DependencyInjection\ContainerInterface
		::EXCEPTION_ON_INVALID_REFERENCE;

		$this->container_get_map[] = array(
				$key,
				$default_parameter,
				$value
		);
	}

	/**
	 * Removes an element from the container mock get map.
	 *
	 * @param string $key The key to be removed.
	 */
	protected function removeFromCointainerMockGetMap($key)
	{
		for ($i = 0, $end = count($this->container_get_map); $i < $end; $i++) {
			if ($this->container_get_map[$i][0] == $key) {
				unset($this->container_get_map[$i]);
			}
		}
	}

	/**
	 * Makes the container start processing 'get' requests.
	 */
	protected function startContainerMockGetter()
	{
		$this->container->expects($this->any())
		->method('get')
		->will($this->returnValueMap($this->container_get_map));
	}
}

