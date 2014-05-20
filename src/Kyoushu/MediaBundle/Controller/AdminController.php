<?php

namespace Kyoushu\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kyoushu\MediaBundle\Table\Type\AdminListEntityDefinitionsType;
use Kyoushu\MediaBundle\Form\Type\AdminEditEntityType;
use Symfony\Component\HttpFoundation\Request;
use Kyoushu\MediaBundle\Entity\MediaEncodeJob;
use Kyoushu\MediaBundle\Admin\Pager;
use Kyoushu\MediaBundle\Form\Type\AdminEntityFinderType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Kyoushu\MediaBundle\Form\Type\AdminListEntityTableWrapperType;
use Kyoushu\MediaBundle\Form\Type\AdminEncodeMediaTableContextType;

class AdminController extends Controller
{
    
    public function indexAction(){
        
        $entityRegistry = $this->get('kyoushu_media.admin.entity_registry');
        
        $table = new AdminListEntityDefinitionsType();
        $table->setData($entityRegistry->getDefinitions());
        
        return $this->render('KyoushuMediaBundle:Admin:index.html.twig', array(
            'table' => $table
        ));
        
    }
    
    public function listAction(Request $request, $entityName, $perPage, $page){
        
        $entityRegistry = $this->get('kyoushu_media.admin.entity_registry');
        
        /* @var $definition \Kyoushu\MediaBundle\Admin\EntityDefinition */
        $definition = $entityRegistry->getDefinition($entityName);
        
        $routeName = 'kyoushu_media_admin_list';
        $routeParameters = array_replace(
            $request->query->all(),
            array(
                'entityName' => $definition->getName(),
                'page' => $page,
                'perPage' => $perPage
            )
        );
        
        $finder = $this->get('kyoushu_media.finder_factory')
            ->createEntityFinder($definition)
            ->setPerPage($perPage)
            ->setPage($page);
        
        $filters = $definition->createListFinderFilters();
            
        $filterForm = null;
        if(count($filters) > 0){
        
            foreach($filters as $filterName => $filter){
                $finder->addFilter($filterName, $filter);
            }
        
            $filterForm = $this->createForm(new AdminEntityFinderType(), $finder, array(
                'action' => $this->generateUrl(
                    $routeName,
                    array_replace(
                        $routeParameters,
                        array('page' => 1)
                    )
                ),
                'method' => 'GET'
            ));
            
            $filterForm->handleRequest($request);
            
        }
        
        $entities = $finder->getResult();
        
        $pager = new Pager($routeName, $routeParameters);
        $pager->setPerPage($perPage);
        $pager->setPage($page);
        $pager->setTotal( $finder->countTotal() );
        
        $table = $definition->createListTable();
        $table->setDefinition($definition);
        $table->setData( $entities );
        
        $tableWrapperForm = $this->createForm(new AdminListEntityTableWrapperType, $table, array(
            'action' => $this->generateUrl($routeName, $routeParameters),
            'context_forms' => $definition->getListContextForms()
        ));
        $table->setFormWrapper($tableWrapperForm);
        
        $tableWrapperForm->handleRequest($request);
        //$selectedIds = array_keys( array_filter( $tableWrapperForm->get('selectedIds')->getData() ) );
        
        $contextFormViews = array();
        foreach($definition->getListContextForms() as $contextForm){
            $contextFormViews[$contextForm->getRevealId()] = $this->
                createForm($contextForm->getFormType(), null, array(
                    'action' => $this->generateUrl($contextForm->getRoute())
                ))->createView();
        }
        
        return $this->render($definition->getListView(), array(
            'definition' => $definition,
            'filter_form' => ($filterForm !== null ? $filterForm->createView() : null),
            'table' => $table,
            'pager' => $pager->createView(),
            'context_forms' => $contextFormViews
        ));
        
    }
    
    public function deleteAction(Request $request, $entityName, $id, $confirm = null){
        
        $entityRegistry = $this->get('kyoushu_media.admin.entity_registry');
        $definition = $entityRegistry->getDefinition($entityName);
        
        $entity = $this->getDoctrine()
            ->getRepository($definition->getClass())
            ->find($id);
        
        if(!$entity){
            throw $this->createNotFoundException('The specified entity could not be found');
        }
        
        if($confirm){
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();
            
            $redirectUrl = $this->generateUrl('kyoushu_media_admin_list', array(
                'entityName' => $entityName
            ));

            $request->getSession()->getFlashBag()->add('notice', sprintf('%s entity removed', $definition->getHumanName()));

            return $this->redirect($redirectUrl);
            
        }
        
        return $this->render('KyoushuMediaBundle:Admin:confirm_delete.html.twig', array(
            'definition' => $definition,
            'entity' => $entity
        ));
        
    }
    
    public function editAction(Request $request, $entityName, $id = null){
        
        $entityRegistry = $this->get('kyoushu_media.admin.entity_registry');
        $definition = $entityRegistry->getDefinition($entityName);
        
        if($id !== null){
            $entity = $this->getDoctrine()
                ->getRepository($definition->getClass())
                ->find($id);
            
            if(!$entity){
                throw $this->createNotFoundException(sprintf(
                    'The %s entity #%s could not be found',
                    $definition->getName(),
                    $id
                ));
            }
            
        }
        else{
            $class = $definition->getClass();
            $entity = new $class;
        }
        
        $form = $this->createForm(new AdminEditEntityType(), $entity, array(
            'action' => $this->generateUrl('kyoushu_media_admin_edit', array(
                'entityName' => $entityName,
                'id' => $id
            )),
            'data_class' => $definition->getClass()
        ));
        
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                
                $redirectUrl = $this->generateUrl('kyoushu_media_admin_list', array(
                    'entityName' => $entityName
                ));
                
                $request->getSession()->getFlashBag()->add('notice', sprintf('Changes to %s entity saved', $definition->getHumanName()));
                
                return $this->redirect($redirectUrl);
                
            }
        }
        
        return $this->render('KyoushuMediaBundle:Admin:edit.html.twig', array(
            'definition' => $definition,
            'entity' => $entity,
            'form' => $form->createView()
        ));
        
    }
   
    public function scanMediaSourceAction(Request $request, $id){
        
        $mediaSource = $this->getDoctrine()
            ->getRepository('KyoushuMediaBundle:MediaSource')
            ->find($id);
        
        if(!$mediaSource){
            throw $this->createNotFoundException('The specified media source could not be found');
        }
        
        $scanner = $this->get('kyoushu_media.scanner');
        $scanner->scanMediaSource($mediaSource);
        
        $request->getSession()->getFlashBag()->add('notice', sprintf(
            'Media source %s scanned',
            $mediaSource->getName()
        ));        
        
        $redirectUrl = $this->generateUrl('kyoushu_media_admin_list', array(
           'entityName' => 'media_source' 
        ));
        
        return $this->redirect($redirectUrl);
        
    }
    
    public function processMediaAction(Request $request, $id){
        
        $media = $this->getDoctrine()
            ->getRepository('KyoushuMediaBundle:Media')
            ->find($id);
        
        if(!$media){
            throw $this->createNotFoundException('The specified media could not be found');
        }
        
        $scanner = $this->get('kyoushu_media.processor');
        $scanner->processMedia($media);
        
        $request->getSession()->getFlashBag()->add('notice', sprintf(
            'Media %s processed',
            $media->getShortDescription()
        ));        
        
        $redirectUrl = $this->generateUrl('kyoushu_media_admin_list', array(
           'entityName' => 'media' 
        ));
        
        return $this->redirect($redirectUrl);
        
    }
    
    public function processTvShowAction(Request $request, $id){
        
        $show = $this->getDoctrine()
            ->getRepository('KyoushuMediaBundle:TvShow')
            ->find($id);
        
        if(!$show){
            throw $this->createNotFoundException('The specified TV show could not be found');
        }
        
        $scanner = $this->get('kyoushu_media.processor');
        $scanner->processTvShow($show);
        
        $request->getSession()->getFlashBag()->add('notice', sprintf(
            'TV show "%s" processed',
            $show->getName()
        ));        
        
        $redirectUrl = $this->generateUrl('kyoushu_media_admin_list', array(
           'entityName' => 'tv_show' 
        ));
        
        return $this->redirect($redirectUrl);
        
    }
    
    public function startMediaEncodeJobAction(Request $request, $id){
        
        $job = $this->getDoctrine()
            ->getRepository('KyoushuMediaBundle:MediaEncodeJob')
            ->find($id);
        
        if(!$job){
            throw $this->createNotFoundException('The specified media encode job could not be found');
        }
        
        $encodeManager = $this->get('kyoushu_media.encoder_manager');
        $encodeManager->startMediaEncodeJob($job);
        
        $request->getSession()->getFlashBag()->add('notice', sprintf(
            'Encode job #%s started',
            $job->getId()
        ));
        
        $redirectUrl = $this->generateUrl('kyoushu_media_admin_list', array(
            'entityName' => 'media_encode_job'
        ));
        
        return $this->redirect($redirectUrl);
        
    }
    
    public function entityAutocompleteAction(Request $request, $entityClass, $property, $searchProperties, $searchString){
        
        $searchProperties = explode(',', $searchProperties);
        
        $responseData = array(
            'datetime' => date('c'),
            'parameters' => array(
                'entityClass' => $entityClass,
                'property' => $property,
                'searchProperties' => $searchProperties,
                'searchString' => $searchString
            ),
            'result' => array()
        );
        
        /* @var $finder \Kyoushu\MediaBundle\Finder\EntityAutocompleteFinder */
        $finder = $this->get('kyoushu_media.finder_factory')->createEntityAutocompleteFinder($entityClass);
        $finder->setSearchProperties($searchProperties);
        $finder->setProperty($property);
        $finder->setSearchString($searchString);
        
        $accessor = PropertyAccess::createPropertyAccessor();
            
        foreach($finder->getResult() as $entity){
            $responseData['result'][] = array(
                'value' => $entity->getId(),
                'label' => $accessor->getValue($entity, $property)
            );
        }
        
        return new JsonResponse($responseData);
        
    }
    
    public function encodeMediaContextAction(Request $request){
        
        $form = $this->createForm(new AdminEncodeMediaTableContextType, null, array(
            'action' => $this->generateUrl('kyoushu_media_admin_list_context_encode_media')
        ));
        
        $form->handleRequest($request);
        
        if($form->isValid()){
            
            $entityIds = $form->get('entityIds')->getData();
            $profileName = $form->get('profile')->getData();
            
            $em = $this->getDoctrine()->getManager();
            
            $mediaResult = $em->getRepository('KyoushuMediaBundle:Media')
                ->createQueryBuilder('m')
                ->andWhere('m.id in (:entity_ids)')
                ->setParameter('entity_ids', $entityIds)
                ->getQuery()
                ->getResult();
            
            
            
            foreach($mediaResult as $media){
                
                $job = new MediaEncodeJob();
                $job->setSourceMedia($media);
                $job->setEncoderProfileName( $profileName );
                $job->setStatus(MediaEncodeJob::STATUS_PENDING);
                
                $em->persist($job);
                
            }
            
            $em->flush();
            
            $request->getSession()->getFlashBag()->add(
                'notice',
                sprintf(
                    '%s encode jobs created',
                    count($mediaResult)
                )
            );
            
            $url = $this->generateUrl('kyoushu_media_admin_list', array(
                'entityName' => 'media_encode_job'
            ));
            
            return $this->redirect($url);
            
        }
        
        return $this->render('KyoushuMediaBundle:Admin:media/context_encode_form.html.twig', array(
           'form' => $form->createView() 
        ));
        
    }
   
    
}
