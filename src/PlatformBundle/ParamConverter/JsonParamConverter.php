<?php
// src/OC/PlatformBundle/ParamConverter/JsonParamConverter.php

namespace App\PlatformBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class JsonParamConverter implements ParamConverterInterface
{
    public function supports(ParamConverter $configuration)
    {
        // Si le nom de l'argument du contrôleur n'est pas "json", on n'applique pas le convertisseur
        if ('json' !== $configuration->getName()) {
            return false;
        }

        return true;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        // On récupère la valeur actuelle de l'attribut
        $json = $request->attributes->get('json');

        // On effectue notre action : le décoder
        //$json = json_decode($json, true);
        $json = json_decode(json_encode($json));

        // On met à jour la nouvelle valeur de l'attribut
        $request->attributes->set('json', $json);
    }
}
