<?php

namespace DAMA\MenuBundle\Twig;

use DAMA\MenuBundle\Node\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MenuExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('dama_menu_render', array($this, 'render'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('dama_menu_section_label', array($this, 'getMenuSectionLabel')),
            new \Twig_SimpleFunction('dama_menu_first_active_child', array($this, 'getFirstActiveChild')),
        );
    }

    public function render(string $name, array $options = array()): string
    {
        $menu = $this->container->get('dama_menu.menu_factory')->create($name);

        $defaultOptions = array(
            'collapse' => false,
            'nested' => true,
        );

        $finalOptions = array_merge($defaultOptions, $options);
        $finalOptions['currentNode'] = $menu;

        return $this->getTemplate($name)->renderBlock('render_root', $finalOptions);
    }

    public function getMenuSectionLabel(string $name): ?string
    {
        $activeChild = $this->getFirstActiveChild($name);

        return null === $activeChild ? '' : $activeChild->getLabel();
    }

    public function getFirstActiveChild(string $name): ?Node
    {
        $menu = $this->container->get('dama_menu.menu_factory')->create($name);

        return $menu ? $menu->getFirstActiveChild() : null;
    }

    protected function getTemplate(string $name): \Twig_Template
    {
        $menuConfig = $this->container->get('dama_menu.menu_config_provider')->getMenuConfig($name);

        return $this->container->get('twig')->loadTemplate($menuConfig['twig_template']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dama_menu_extension';
    }
}
