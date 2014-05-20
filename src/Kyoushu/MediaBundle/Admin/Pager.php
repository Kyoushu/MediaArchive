<?php

namespace Kyoushu\MediaBundle\Admin;

class Pager {
    
    private $perPage;
    private $page;
    private $pageNumberOverflow;
    
    private $route;
    private $defaultParameters;
    private $perPageProperty;
    private $pageProperty;
    
    public function __construct($route, array $defaultParameters = array(), $perPageProperty = 'perPage', $pageProperty = 'page'){
        
        $this->total = 0;
        $this->perPage = 20;
        $this->pageNumberOverflow = 5;
        $this->page = 1;
        
        $this->route = $route;
        $this->defaultParameters = $defaultParameters;
        $this->perPageProperty = $perPageProperty;
        $this->pageProperty = $pageProperty;
        
    }
    
    public function setTotal($total){
        $this->total = (int)$total;
        return $this;
    }
    
    public function getTotal(){
        if($this->total < 0) return 0;
        return $this->total;
    }
    
    public function getPerPage() {
        if($this->perPage < 1) return 1;
        return $this->perPage;
    }

    public function getPage(){
        if($this->page < 1) return 1;
        return $this->page;
    }

    public function setPerPage($perPage) {
        $this->perPage = (int)$perPage;
        return $this;
    }

    public function setPage($page) {
        $this->page = (int)$page;
        return $this;
    }
    
    public function getTotalPages(){
        $total = $this->getTotal();
        $perPage = $this->getPerPage();
        return ceil($total / $perPage);
    }
    
    public function getPageNumberOverflow(){
        if($this->pageNumberOverflow < 0) return 0;
        return $this->pageNumberOverflow;
    }

    public function setPageNumberOverflow($pageNumberOverflow) {
        $this->pageNumberOverflow = $pageNumberOverflow;
        return $this;
    }  
    
    public function getRoute() {
        return $this->route;
    }

    public function getDefaultParameters() {
        return $this->defaultParameters;
    }
    
    public function createView(){
        
        $pageNumberOverflow = $this->getPageNumberOverflow();
        $totalPages = $this->getTotalPages();
        
        $route = $this->getRoute();
        $defaultParameters = $this->getDefaultParameters();
        
        $current = $this->getPage();
        
        $start = $current - $pageNumberOverflow;
        if($start < 1) $start = 1;
        
        $end = $current + $pageNumberOverflow;
        if($end > $totalPages) $end = $totalPages;
        
        $view = array();
        
        $perPage = $this->getPerPage();
        
        $perPageProperty = $this->perPageProperty;
        $pageProperty = $this->pageProperty;
        
        $view[] = array(
            'attr' => array('class' => 'arrow' . ($current === 1 ? ' unavailable' : '')),
            'text' => '«',
            'route' => ($current === 1 ? null : $route),
            'parameters' => array_replace(
                $defaultParameters,
                array(
                    $perPageProperty => $perPage,
                    $pageProperty => ($current > 1 ? $current - 1 : $current)
                )
            )
        );
        
        if($start > 1){
            
            $view[] = array(
                'attr' => array(),
                'text' => 1,
                'route' => $route,
                'parameters' => array_replace(
                    $defaultParameters,
                    array(
                        $perPageProperty => $perPage,
                        $pageProperty => 1
                    )
                )
            );
            
            $view[] = array(
                'attr' => array('class' => 'unavailable'),
                'text' => '…',
                'route' => null,
                'parameters' => null
            );
            
        }
        
        for($page = $start; $page <= $end; $page++){
            $view[] = array(
                'attr' => array('class' => ($page === $current ? 'current' : '')),
                'text' => $page,
                'route' => $route,
                'parameters' => array_replace(
                    $defaultParameters,
                    array(
                        $perPageProperty => $perPage,
                        $pageProperty => $page
                    )
                )
            );
        }
        
        if($end < $totalPages){
            
            $view[] = array(
                'attr' => array('class' => 'unavailable'),
                'text' => '…',
                'route' => null,
                'parameters' => null
            );
            
            $view[] = array(
                'attr' => array(),
                'text' => $totalPages,
                'route' => $route,
                'parameters' => array_replace(
                    $defaultParameters,
                    array(
                        $perPageProperty => $perPage,
                        $pageProperty => $totalPages
                    )
                )
            );
            
        }
        
        $view[] = array(
            'attr' => array('class' => 'arrow' . ($current >= $totalPages ? ' unavailable' : '')),
            'text' => '»',
            'route' => ($current === $totalPages ? null : $route),
            'parameters' => array_replace(
                $defaultParameters,
                array(
                    $perPageProperty => $perPage,
                    $pageProperty => ($current < $totalPages ? $current + 1 : $current)
                )
            )
        );
        
        return $view;
        
    }
    
}
