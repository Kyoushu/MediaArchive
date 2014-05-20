<?php

namespace Kyoushu\MediaBundle\Admin;

use Kyoushu\MediaBundle\Admin\EntityDefinition;

class ListContextForm {
    
    private $name;
    private $definition;
    private $formClass;
    private $buttonLabel;
    private $route;
    
    public function __construct(EntityDefinition $definition, $name, $config){
        $this->definition = $definition;
        $this->name = $name;
        $this->formClass = $config['form_class'];
        $this->buttonLabel = $config['button_label'];
        $this->route = $config['route'];
    }
    
    /**
     * Get name
     * @return string
     */
    public function getName(){
        return $this->name;
    }
    
    /**
     * Get route
     * @return string
     */
    public function getRoute() {
        return $this->route;
    }
        
    /**
     * @return string
     */
    public function getRevealId(){
        return sprintf('admin_list_context_%s', $this->name);
    }
    
    /**
     * Get definition
     * @return \Kyoushu\MediaBundle\Admin\EntityDefinition
     */
    public function getDefinition() {
        return $this->definition;
    }

    /**
     * Get formClass
     * @return string
     */
    public function getFormClass() {
        return $this->formClass;
    }
    
    /**
     * Get form type object
     * @return Symfony\Component\Form\AbstractType
     */
    public function getFormType(){
        $class = $this->getFormClass();
        return new $class;
    }

    /**
     * Get buttonLabel
     * @return string
     */
    public function getButtonLabel() {
        return $this->buttonLabel;
    }


    
}
