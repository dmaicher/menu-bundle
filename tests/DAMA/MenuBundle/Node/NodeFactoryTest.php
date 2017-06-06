<?php

namespace DAMA\MenuBundle\Tests\Node;

use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\Node\NodeFactory;

class NodeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = new NodeFactory();

        $node = $factory->create('label');

        $this->assertInstanceOf(Node::class, $node);
        $this->assertSame('label', $node->getLabel());
    }
}
