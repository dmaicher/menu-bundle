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
            'dama_menu_render' => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('dama_menu_section_label', array($this, 'getMenuSectionLabel')),
            new \Twig_SimpleFunction('dama_menu_first_active_child', array($this, 'getFirstActiveChild')),
        );
    }

    /**
     * @param string $name
     * @param array $options
     *
     * @return string
     */
    public function render($name, array $options = array())
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

    /**
     * @param string $name
     *
     * @return string
     */
    public function getMenuSectionLabel($name)
    {
        $activeChild = $this->getFirstActiveChild($name);

        return null === $activeChild ? '' : $activeChild->getLabel();
    }

    /**
     * @param string $name
     *
     * @return Node
     */
    public function getFirstActiveChild($name)
    {
        $menu = $this->container->get('dama_menu.menu_factory')->create($name);

        return $menu ? $menu->getFirstActiveChild() : null;
    }

    /**
     * @param string $name
     *
     * @return \Twig_Template
     */
    protected function getTemplate($name)
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
