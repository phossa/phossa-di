<?php
namespace Phossa\Di\Extension\Provider;

class TestTagProvider extends ProviderAbstract
{
    const PROVIDER_CLASS = __CLASS__;

    /**
     * Services we provide
     *
     * @var    string[]
     * @access protected
     */
    protected $provides = [ 'bingo' ];

    /**
     * Tags, empty means match all tags
     *
     * @var    string[]
     * @access protected
     */
    protected $tags = [ 'TEST' ];

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
        $this->getContainer()->add('bingo', 'bingoXX');
    }
}
