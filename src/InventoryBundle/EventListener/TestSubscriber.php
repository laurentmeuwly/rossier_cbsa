<?php

namespace InventoryBundle\EventListener;

use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Doctrine\Common\Collections\Criteria;

class TestSubscriber implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return [
				EasyAdminEvents::POST_LIST_QUERY_BUILDER => 'productImageWithoutNull',
		];
	}
	
	public function productImageWithoutNull(GenericEvent $event)
	{
		// get necessary variables from the event
		$subject = $event->getSubject();
		$sortField = $event->getArgument('sort_field');
		
		// apply only when user is sorting by image in the Product entity
		if ($subject['name'] !== 'Product' || $sortField !== 'image') {
			return;
		}
		
		// filter out NULL values from the result set
		$queryBuilder = $event->getArgument('query_builder');
		$criteria = Criteria::create()->where(Criteria::expr()->neq('image', null));
		$queryBuilder->addCriteria($criteria);
		
	}
	
}