<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\FormFilterBundle\Event\GetFilterConditionEvent;

class LexikFilterSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'lexik_form_filter.apply.orm.no_ui_slider_filter' => ['sliderRangeFilter'],
        ];
    }

    public function sliderRangeFilter(GetFilterConditionEvent $event)
    {
        $expr   = $event->getFilterQuery()->getExpr();
        $values = $event->getValues()['value'];
        $expression = $expr->andX();
        $parameters = [];

        if (strlen($values['min']) > 0 && strlen($values['max']) > 0) {
            $minParameterName = sprintf('p_%s_min', str_replace('.', '_', $event->getField()));
            $expression->add($expr->gte($event->getField(), ':'.$minParameterName));
            $parameters[$minParameterName] = $values['min'];
            $maxParameterName = sprintf('p_%s_max', str_replace('.', '_', $event->getField()));
            $expression->add($expr->lte($event->getField(), ':'.$maxParameterName));
            $parameters[$maxParameterName] = $values['max'];
        }
        
        if ($expression->count()) {
            $event->setCondition($expression, $parameters);
        }
    }

}
