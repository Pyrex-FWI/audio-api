<?php

namespace AppBundle\Doctrine\Orm;

use Doctrine\ORM\QueryBuilder;
use Dunglas\ApiBundle\Api\ResourceInterface;
use Dunglas\ApiBundle\Doctrine\Orm\Filter\AbstractFilter;
use Symfony\Component\HttpFoundation\Request;

class MediaUntagedFilter extends AbstractFilter
{
    /**
     * Applies the filter.
     *
     * @param ResourceInterface $resource
     * @param QueryBuilder      $queryBuilder
     * @param Request           $request
     */
    public function apply(ResourceInterface $resource, QueryBuilder $queryBuilder, Request $request)
    {
        $properties = $this->extractProperties($request);
        if (isset($properties['untaged']) && $properties['untaged'] == 1) {
            $queryBuilder
                ->andWhere(sprintf('(o.tagged = 0)'))
            ;
        }
    }

    /**
     * Gets the description of this filter for the given resource.
     *
     * Returns an array with the filter parameter names as keys and array with the following data as values:
     *   - property: the property where the filter is applied
     *   - type: the type of the filter
     *   - required: if this filter is required
     *   - strategy: the used strategy
     * The description can contain additional data specific to a filter.
     *
     * @param ResourceInterface $resource
     *
     * @return array
     */
    public function getDescription(ResourceInterface $resource)
    {
        return [];
    }
}
