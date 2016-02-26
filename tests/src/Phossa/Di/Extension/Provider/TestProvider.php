<?php
namespace Phossa\Di\Extension\Provider;

class TestProvider extends ProviderAbstract
{
    const PROVIDER_CLASS = __CLASS__;

    /**
     * Services we provide
     *
     * @var    string[]
     * @access protected
     */
    protected $provides = [ 'XX' ];

    /**
     * Child class implements this method to merge defintions with container
     *
     * @return void
     * @throws LogicException if merging goes wrong
     * @access protected
     */
    protected function merge()
    {
        // define service to 'bingoXX' class
        $this->getContainer()->add('XX', 'bingoXX');
    }
}
