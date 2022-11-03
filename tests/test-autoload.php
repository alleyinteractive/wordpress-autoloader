<?php
/**
 * Autoload test file
 *
 * @package WordPress_Autoloader
 */

namespace Alley_Interactive\Autoloader\Tests;

use PHPUnit\Framework\TestCase;
use Alley_Interactive\Autoloader\Autoloader;

/**
 * Autoloader Test Case
 */
class Test_Autoload extends TestCase {
	/**
	 * Generated autoloader.
	 *
	 * @var Autoloader
	 */
	protected $autoloader;

	/**
	 * Set up, generate the autoloader.
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->autoloader = Autoloader::generate(
			__NAMESPACE__ . '\\Autoloaded',
			__DIR__ . '/includes',
		);
	}

	/**
	 * Tear down, make sure the autoloader is unregistered.
	 */
	protected function tearDown(): void {
		parent::tearDown();
		\spl_autoload_unregister( $this->autoloader );
	}

	/**
	 * Register the autoloader.
	 */
	protected function register_autoloader() {
		try {
			\spl_autoload_register( $this->autoloader );
		} catch ( \Exception $exception ) {
			$this->fail( $exception->getMessage() );
		}
	}

	/**
	 * Tests that a function returns true only after registering the autoloader.
	 *
	 * @param callable $func Function to test.
	 * @param array    $args Array of function arguments.
	 */
	protected function assert_true_after_registering_autoloader( callable $func, array $args ) {
		$this->assertFalse( \call_user_func_array( $func, $args ) );
		$this->register_autoloader();
		$this->assertTrue( \call_user_func_array( $func, $args ) );
	}

	/**
	 * Test that a class is autoloaded.
	 */
	public function test_autoload_class() {
		$this->assertFalse(
			class_exists( __NAMESPACE__ . '\Autoloaded\Autoloaded_Class' ),
		);

		$this->assert_true_after_registering_autoloader(
			'class_exists',
			[ __NAMESPACE__ . '\Autoloaded\Autoloaded_Class' ]
		);
	}

	/**
	 * Test that a missing class is remembered when it is not found.
	 */
	public function test_autoload_class_missing() {
		$this->assertFalse(
			class_exists( __NAMESPACE__ . '\Autoloaded\Missing_Class' ),
		);

		$this->assertFalse(
			$this->autoloader->is_missing_class( __NAMESPACE__ . '\Autoloaded\Missing_Class' ),
		);

		$this->autoloader->register();

		$this->assertFalse(
			class_exists( __NAMESPACE__ . '\Autoloaded\Missing_Class' ),
		);

		$this->assertTrue(
			$this->autoloader->is_missing_class( __NAMESPACE__ . '\Autoloaded\Missing_Class' ),
		);
	}

	/**
	 * Test that a class is ignored if it doesn't match the provided namespace.
	 */
	public function test_ignore_other_namespaces() {
		$this->register_autoloader();
		$this->assertFalse( \class_exists( '\This\Is\Not\A\Class' ) );
	}

	/**
	 * Test that a sub-namespaced class is autoloaded.
	 */
	public function test_autoload_subnamespaced_class() {
		$this->assert_true_after_registering_autoloader(
			'class_exists',
			[ __NAMESPACE__ . '\Autoloaded\Subnamespaced\Autoloaded_Class' ]
		);
	}

	/**
	 * Test that a class in a sub-namespace containing underscores is autoloaded.
	 */
	public function test_autoload_subnamespaced_with_underscores_class() {
		$this->assert_true_after_registering_autoloader(
			'class_exists',
			[ __NAMESPACE__ . '\Autoloaded\Subnamespaced\With_Underscores\Autoloaded_Class' ]
		);
	}

	/**
	 * Test that a trait is autoloaded.
	 */
	public function test_autoload_trait() {
		$this->assert_true_after_registering_autoloader(
			'trait_exists',
			[ __NAMESPACE__ . '\Autoloaded\Autoloaded_Trait' ]
		);
	}

	/**
	 * Test that an interface is autoloaded.
	 */
	public function test_autoload_interface() {
		$this->assert_true_after_registering_autoloader(
			'interface_exists',
			[ __NAMESPACE__ . '\Autoloaded\Autoloaded_Interface' ]
		);
	}

	/**
	 * Test registering the autoloader through the register method.
	 */
	public function test_register_method() {
		$this->assertFalse(
			class_exists( __NAMESPACE__ . '\Autoloaded\Other_Autoloaded_Class' ),
		);

		$this->autoloader->register();

		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Autoloaded\Other_Autoloaded_Class' ),
		);
	}

	/**
	 * Test using backtrace to determine the namespace that should be used
	 */
	public function test_get_calling_file_namespace() {

		$sut = new Autoloader( 'namespace', 'path' );

		// $method = new \ReflectionMethod( Autoloader::class, 'get_calling_file_namespace' );
		// $method->setAccessible( true );
		//
		// $result = $method->invoke( $sut );
		$result = $sut->get_calling_file_namespace();

		$this->assertEquals( __NAMESPACE__, $result );
	}
}
