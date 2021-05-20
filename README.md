dama/menu-bundle
==============

This bundle can be used to build dynamic menus based on granted permissions.

Step 1: create MenuTreeBuilder
------------------------------

```php
class MainMenuTreeBuilder implements MenuTreeBuilderInterface
{
    public function buildTree(Node $root)
    {
        $root
            ->child('social_media')
                ->setAttr('id', 'main_menu_social_media')
                ->setRequiredPermissions(['ROLE_SOCIAL_MENU'])
                ->child('stream')
                    ->setRoute('social_media_stream')
                    ->setRequiredPermissions(['ROLE_SOCIAL_STREAM'])
                ->end()
                ->child('update_status')
                    ->setRoute('social_media_update_status')
                    ->setRequiredPermissions(['ROLE_SOCIAL_UPDATE_STATUS'])
                ->end()
                ->ifTrue($someCondition) // only add child node(s) inside if the condition is true
                    ->child('statistics')
                        ->setRoute('social_media_statistics')
                        ->setRequiredPermissions([new Expression("has_role('ROLE_USER')")])
                    ->end()
                ->endIf()
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
        my_main_menu:
            tree_builder: Your\Namespace\MainMenuTreeBuilder #service ID OR FQCN and no constructor args
            twig_template: YourNamespace:my_main_menu.html.twig #optional
```

    
Step 3: render the menu
-----------------------

```twig
{{ dama_menu_render('my_main_menu', {'collapse':true, 'nested':false}) }}
```
