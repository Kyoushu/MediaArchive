<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntityAutocompleteType extends AbstractType
{
    
    public function buildView(FormView $view, FormInterface $form, array $options) {
        parent::buildView($view, $form, $options);
        
        $view->vars['class'] = $options['class'];
        $view->vars['property'] = $options['property'];
        $view->vars['search_properties'] = $options['search_properties'];
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver){
        
        $resolver->setRequired(array(
            'search_properties',
            'property'
        ));
    }
    
    public function getParent(){
        return 'entity';
    }
    
    public function getName() {
        return 'entity_autocomplete';
    }
    
}