<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Kyoushu\MediaBundle\Form\Mapping as Form;

class AnnotationReaderType extends AbstractType
{
    
    public function buildFormFromReflectionClass(\ReflectionClass $classRef, FormBuilderInterface $builder, array $options){
        
        $reader = new AnnotationReader();
        
        $fieldDefinitions = array();
        
        foreach($classRef->getProperties() as $propertyRef){
            
            $name = $propertyRef->getName();
            
            foreach($reader->getPropertyAnnotations($propertyRef) as $annotation){
                if(!$annotation instanceof Form\Field) continue;
                
                $fieldDefinitions[] = array(
                    'annotation' => $annotation,
                    'name' => $name
                );
                
            }
            
        }
        
        usort($fieldDefinitions, function($a, $b){
            if($a['annotation']->weight === $b['annotation']->weight) return 0;
            return ($a['annotation']->weight > $b['annotation']->weight ? 1 : -1);
        });
        
        foreach($fieldDefinitions as $fieldDefinition){
            $builder->add(
                $fieldDefinition['name'],
                $fieldDefinition['annotation']->type,
                $fieldDefinition['annotation']->options
            );
        }
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $classRef = new \ReflectionClass($options['data_class']);
        $this->buildFormFromReflectionClass($classRef, $builder, $options);
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
        $resolver->setRequired(array('data_class'));
        
    }
    
    public function getName() {
        return 'annotation_reader';
    }
    
}