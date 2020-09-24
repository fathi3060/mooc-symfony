<?php
// src/Form/AdvertEditType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdvertType;

class AdvertEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('date');
    }

    public function getParent()
    {
        return AdvertType::class;
    }
}
