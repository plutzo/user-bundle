<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Marlinc\UserBundle\Model\GroupInterface;
use Marlinc\UserBundle\Model\GroupManagerInterface;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class GroupManager implements GroupManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * GroupManager constructor.
     *
     * @param ObjectManager $om
     * @param string        $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);

        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function createGroup($name): GroupInterface
    {
        $class = $this->getClass();

        return new $class($name);
    }

    /**
     * {@inheritdoc}
     */
    public function updateGroup(GroupInterface $group, $andFlush = true): GroupManagerInterface
    {
        $this->objectManager->persist($group);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup(GroupInterface $group): GroupManagerInterface
    {
        $this->objectManager->remove($group);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findGroupBy(array $criteria): ?GroupInterface
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findGroups(): array
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findGroupByName($name): GroupInterface
    {
        return $this->findGroupBy(['name' => $name]);
    }

    /**
     * {@inheritdoc}
     */
    public function findGroupsBy(array $criteria = null, array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        $query = $this->repository
            ->createQueryBuilder('g')
            ->select('g');

        $fields = $this->objectManager->getClassMetadata($this->class)->getFieldNames();
        foreach ($sort as $field => $direction) {
            if (!in_array($field, $fields)) {
                throw new \RuntimeException(sprintf("Invalid sort field '%s' in '%s' class", $field, $this->class));
            }
        }

        if (0 == count($sort)) {
            $sort = ['name' => 'ASC'];
        }

        foreach ($sort as $field => $direction) {
            $query->orderBy(sprintf('g.%s', $field), strtoupper($direction));
        }

        $parameters = [];

        if (isset($criteria['enabled'])) {
            $query->andWhere('g.enabled = :enabled');
            $parameters['enabled'] = $criteria['enabled'];
        }

        $query->setParameters($parameters);

        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}
