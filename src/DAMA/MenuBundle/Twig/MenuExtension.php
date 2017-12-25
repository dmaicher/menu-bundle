<?php

namespace DAMA\MenuBundle\Twig;

use DAMA\MenuBundle\Menu\MenuFactoryInterface;
use DAMA\MenuBundle\MenuConfig\MenuConfigProvider;
use DAMA\MenuBundle\Node\Node;

class MenuExtension extends \Twig_Extension
{
    /**
     * @var MenuFactoryInterface
     */
    private $menuFactory;

    /**
     * @var MenuConfigProvider
     */
    private $configProvider;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(
        \Twig_Environment $twig,
        MenuFactoryInterface $menuFactory,
        MenuConfigProvider $configProvider
    ) {
        $this->menuFactory = $menuFactory;
        $this->configProvider = $configProvider;
        $this->twig = $twig;
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

    /**
     * @param string $name
     * @param array  $options
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     */
    public function render($name, array $options = array())
    {
        $menu = $this->menuFactory->create($name);

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
        $menu = $this->menuFactory->create($name);

        return $menu ? $menu->getFirstActiveChild() : null;
    }

    /**
     * @param string $name
     *
     * @return \Twig_Template
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function getTemplate($name)
    {
        $menuConfig = $this->configProvider->getMenuConfig($name);

        return $this->twig->loadTemplate($menuConfig['twig_template']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dama_menu_extension';
    }
}
