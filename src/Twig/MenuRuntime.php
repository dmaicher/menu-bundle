<?php

namespace DAMA\MenuBundle\Twig;

use DAMA\MenuBundle\Menu\MenuFactory;
use DAMA\MenuBundle\MenuConfig\MenuConfigProvider;
use DAMA\MenuBundle\Node\Node;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\TemplateWrapper;

final class MenuRuntime implements RuntimeExtensionInterface
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var MenuFactory
     */
    private $menuFactory;

    /**
     * @var MenuConfigProvider
     */
    private $menuConfigProvider;

    public function __construct(Environment $twig, MenuFactory $menuFactory, MenuConfigProvider $menuConfigProvider)
    {
        $this->twig = $twig;
        $this->menuFactory = $menuFactory;
        $this->menuConfigProvider = $menuConfigProvider;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function render(string $name, array $options = []): string
    {
        $menu = $this->menuFactory->create($name);

        $defaultOptions = [
            'collapse' => false,
            'nested' => true,
        ];

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
        return $this->menuFactory->create($name)->getFirstActiveChild();
    }

    private function getTemplate(string $name): TemplateWrapper
    {
        $menuConfig = $this->menuConfigProvider->getMenuConfig($name);

        return $this->twig->load($menuConfig['twig_template']);
    }
}
