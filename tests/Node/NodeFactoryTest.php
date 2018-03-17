<?php

namespace Tests\Node;

use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\Node\NodeFactory;
use PHPUnit\Framework\TestCase;

class NodeFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = new NodeFactory();

        $node = $factory->create('label');

        $this->assertInstanceOf(Node::class, $node);
        $this->assertSame('label', $node->getLabel());
    }
}
