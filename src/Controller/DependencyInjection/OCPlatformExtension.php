<?php

namespace App\Controller\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
//use Symfony\Component\Config\Definition\Exception\Exception;
//use Symfony\Component\Config\Definition\ConfigurationInterface;

class OCPlatformExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
    
        //$configuration = new Configuration();
       // $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
