<?php
namespace Phossa\Di\Extension\Provider;

class TestTagProvider extends ProviderAbstract
{
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
     * @abstract
     */
    protected function mergeDefinition()
    {
        // define service to 'bingoXX' class
        $this->getContainer()->add('bingo', 'bingoXX');
    }
}
