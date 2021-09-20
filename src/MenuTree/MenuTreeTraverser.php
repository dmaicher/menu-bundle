<?php

namespace DAMA\MenuBundle\MenuTree;

use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\NodeVisitor\NodeVisitorInterface;

/**
 * @final
 */
class MenuTreeTraverser implements MenuTreeTraverserInterface
{
    /**
     * @var NodeVisitorInterface[]
     */
    protected $visitors = [];

    public function traverse(Node $node): void
    {
        if (!$node->isRootNode()) {
            foreach ($this->visitors as $visitor) {
                $result = $visitor->visit($node);
                if ($result === self::STOP_TRAVERSAL) {
                    return;
                }
            }
        }

        foreach ($node->getChildren() as $child) {
            $this->traverse($child);
        }
    }

    public function addVisitor(NodeVisitorInterface $visitor): void
    {
        $this->visitors[] = $visitor;
    }

    /**
     * @return array<NodeVisitorInterface>
     */
    public function getVisitors(): array
    {
        return $this->visitors;
    }
}
