<?php

namespace DAMA\MenuBundle\MenuTree;

use DAMA\MenuBundle\Node\Node;

interface MenuTreeBuilderInterface
{
    public function build(Node $root);
}
