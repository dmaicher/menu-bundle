<?php

namespace DAMA\MenuBundle\Twig;

use DAMA\MenuBundle\Node\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TemplateWrapper;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('dama_menu_render', array($this, 'render'), array('is_safe' => array('html'))),
            new TwigFunction('dama_menu_section_label', array($this, 'getMenuSectionLabel')),
            new TwigFunction('dama_menu_first_active_child', array($this, 'getFirstActiveChild')),
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

        return $activeChild ? $activeChild->getLabel() : '';
    }

    public function getFirstActiveChild(string $name): ?Node
    {
        $menu = $this->container->get('dama_menu.menu_factory')->create($name);

        return $menu ? $menu->getFirstActiveChild() : null;
    }

    protected function getTemplate(string $name): TemplateWrapper
    {
        $menuConfig = $this->container->get('dama_menu.menu_config_provider')->getMenuConfig($name);

        return $this->container->get('twig')->load($menuConfig['twig_template']);
    }

    public function getName(): string
    {
        return 'dama_menu_extension';
    }
}
