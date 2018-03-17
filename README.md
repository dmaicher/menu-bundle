dama/menu-bundle
==============

This bundle can be used to build dynamic menus.

Step 1: create MenuTreeBuilder
------------------------------

```php
class MainMenuTreeBuilder implements MenuTreeBuilderInterface
{
    public function buildTree(Node $root)
    {
        $root
            ->child('social_media')
                ->setAttr('id', 'main_menu_socialMedia')
                ->setRequiredPermissions(['ROLE_SOCIAL_MENU'])
                ->child('stream')
                    ->setRoute('_social_media_stream')
                    ->setRequiredPermissions(['ROLE_SOCIAL_STREAM'])
                ->end()
                ->child('update_status')
                    ->setRoute('_social_media_update_status')
                    ->setRequiredPermissions(['ROLE_SOCIAL_UPDATE_STATUS'])
                ->end()
                ->child('statistics')
                    ->setRoute('_social_media_statistics')
                    ->setRequiredPermissions([new Expression("has_role('ROLE_USER')")])
                ->end()
            ->end()
        ;
    }
}
```
    
    
Step 2: add config for your menu
-----------------------

```ỳaml
dama_menu:
    menues:
        your_namespace.main_menu:
            tree_builder: Your\Namespace\MainMenuTreeBuilder #service ID OR FQCN and no constructor args
            twig_template: YourNamespace:main_menu.html.twig #optional
```

    
Step 3: render the menu
-----------------------

```twig
{{ dama_menu_render('your_namespace.main_menu', {'collapse':true, 'nested':false}) }}
```
