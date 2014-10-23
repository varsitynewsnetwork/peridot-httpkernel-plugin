<?php
namespace Peridot\Plugin\Silex;

use Evenement\EventEmitterInterface;
use Peridot\Runner\Context;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SilexPlugin
{
    /**
     * @var EventEmitterInterface
     */
    protected $emitter;

    /**
     * @var SilexScope
     */
    protected $scope;

    /**
     * @param EventEmitterInterface $emitter the peridot event emitter
     * @param HttpKernelInterface|callable $factory an HttpKernelInterface or a callable that returns one
     * @param string $property the name of the scope property to be used
     */
    public function __construct(EventEmitterInterface $emitter, $factory, $property = "client")
    {
        $this->emitter = $emitter;
        $this->scope = new SilexScope($factory, $property);
    }

    /**
     * When the runner starts we will mix in the silex scope into the root suite,
     * thereby making it available EVERYWHERE.
     */
    public function onRunnerStart()
    {
        $rootSuite = Context::getInstance()->getCurrentSuite();
        $rootSuite->getScope()->peridotAddChildScope($this->scope);
    }

    /**
     * @param EventEmitterInterface $emitter
     * @param HttpKernelInterface|callable  $factory
     */
    public static function register(EventEmitterInterface $emitter, $factory, $property = "client")
    {
        $plugin = new static($emitter, $factory, $property);
        $emitter->on('runner.start', [$plugin, 'onRunnerStart']);
        return $plugin;
    }
} 
