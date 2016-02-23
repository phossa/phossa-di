<?php
namespace Phossa\Di\Extension\Provider;

class TestProvider extends ProviderAbstract
{
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
     * @abstract
     */
    protected function mergeDefinition()
    {
        // define service to 'bingoXX' class
        $this->getContainer()->add('XX', 'bingoXX');
    }
}
