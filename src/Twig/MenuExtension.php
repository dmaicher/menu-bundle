<?php

namespace DAMA\MenuBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @final
 */
class MenuExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('dama_menu_render', [MenuRuntime::class, 'render'], ['is_safe' => ['html']]),
            new TwigFunction('dama_menu_section_label', [MenuRuntime::class, 'getMenuSectionLabel']),
            new TwigFunction('dama_menu_first_active_child', [MenuRuntime::class, 'getFirstActiveChild']),
        ];
    }

    public function getName(): string
    {
        return 'dama_menu_extension';
    }
}
