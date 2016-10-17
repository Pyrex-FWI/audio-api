<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace Pyrex\CoreModelBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Knp\Component\Pager\Paginator;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * AbstractCoreRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
abstract class AbstractCoreRepository extends EntityRepository implements PaginatorAwareInterface
{

    /** @var  ValidatorInterface */
    protected $validator;
    /** @var  LoggerInterface */
    protected $logger;
    /** @var  Paginator */
    protected $paginator;
    /** @var  FilterBuilderUpdaterInterface */
    protected $filterBuilderUpdaterInterface;

    /**
     * AbstractCoreRepository constructor.
     * @param EntityManager         $em
     * @param Mapping\ClassMetadata $class
     */
    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->logger = new NullLogger();
    }


    /**
     * @param object $entity
     * @param bool   $check
     * @return bool
     */
    public function save($entity, $check = true)
    {
        $this->validate($entity, $check);
        $new = $entity->getId();
        $this->_em->persist($entity);
        $this->_em->flush($entity);
        $this->logger->info(sprintf('#%d was %s', $entity->getId(), $new ? 'created' : 'saved'), (array) $entity);

        return  true;
    }

    /**
     * @param object $entity
     * @param bool   $check
     */
    public function merge($entity, $check = true)
    {
        $this->validate($entity, $check);
        $this->_em->merge($entity);
        //$this->_em->persist($entity);
        $this->_em->flush();
        $this->logger->info(sprintf('#%d was %s', $entity->getId(), 'merged'), (array) $entity);
    }

    /**
     * @param ValidatorInterface $validation
     */
    public function setValidator(ValidatorInterface $validation)
    {
        $this->validator = $validation;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Paginator $paginator
     * @return null
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @param FilterBuilderUpdaterInterface $filterBuilderUpdaterInterface
     * @return null
     */
    public function setFilterBuilderUpdater(FilterBuilderUpdaterInterface $filterBuilderUpdaterInterface)
    {
        $this->filterBuilderUpdaterInterface = $filterBuilderUpdaterInterface;
    }

    /**
     * @param $entity
     * @param $check
     */
    public function validate($entity, $check)
    {
        $errors = $check ? $this->validator->validate($entity) : [];
        if (count($errors) > 0) {
            throw new ValidatorException((string) $errors);
        }
    }

    /**
     * @param Request       $request
     * @param FormInterface $formFilter
     * @param int           $limit
     * @param string        $queryParam
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function paginate(Request $request, FormInterface $formFilter = null, $limit = 100, $queryParam = 'page')
    {
        $dql   = sprintf("SELECT e FROM %s e", $this->_class->getName());
        $em    = &$this->_em;
        $query = $em->createQuery($dql);
        if ($formFilter) {
            $formFilter->handleRequest($request);
            if ($formFilter->isSubmitted() && $formFilter->isValid()) {
                $filterBuilder = $this->createQueryBuilder('e');
                $this->filterBuilderUpdaterInterface->addFilterConditions($formFilter, $filterBuilder);
                $query = $filterBuilder->getQuery();
            }
        }
        dump($query->getDQL());

        return $this->paginator->paginate(
            $query,
            $request->query->getInt($queryParam, 1)/*page number*/,
            $limit
        );
    }

}
