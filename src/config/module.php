<?php

return [
    'name' => 'Example Plugin',
    'repository' => 'https://github.com/Nayori-jp/Nayori.Workspaces.ExamplePlugin',
    'version' => '0.1.0',
    'enabled' => true,
    'type' => 'addon',
    'route' => 'example-plugin.examples.index',
    'provider' => \Plugins\ExamplePlugin\ExamplePluginServiceProvider::class,
];
