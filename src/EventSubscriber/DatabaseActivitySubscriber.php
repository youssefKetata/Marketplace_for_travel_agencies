<?php

namespace App\EventSubscriber;

use App\Entity\Fiscalyear;
use App\Entity\FiscalyearPeriod;
use App\Entity\Module;
use App\Entity\ModuleFiscalyear;
use App\Entity\Period;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityRepository;

class DatabaseActivitySubscriber implements EventSubscriberInterface
{
    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
        ];
    }
    /*
    Event	    Dispatched by	Lifecycle                           Callback	        Passed      Argument
    preRemove	$em->remove()	Yes	                                LifecycleEventArgs
    postRemove	$em->flush()	Yes	                                LifecycleEventArgs
    prePersist	$em->persist() on initial persist	Yes	            LifecycleEventArgs
    postPersist	$em->flush()	Yes	                                LifecycleEventArgs
    preUpdate	$em->flush()	Yes	                                PreUpdateEventArgs
    postUpdate	$em->flush()	Yes	                                LifecycleEventArgs
    postLoad	Loading from database	Yes	                        LifecycleEventArgs
    loadClassMetadata	Loading of mapping metadata	No	            LoadClassMetadataEventArgs
    onClassMetadataNotFound	MappingException	No	                OnClassMetadataNotFoundEventArgs
    preFlush	$em->flush()	Yes	                                PreFlushEventArgs
    onFlush	$em->flush()	No	                                    OnFlushEventArgs
    postFlush	$em->flush()	No	                                PostFlushEventArgs
    onClear	$em->clear()	No	                                    OnClearEventArgs
     */

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args): void
    {
        // Actions insert
        $this->eventInsert( $args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        // Actions Remove
        //$this->eventDelete( $args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        // Action Update
        //$this->eventUpdate( $args);
    }


    private function eventInsert(LifecycleEventArgs $args): void
    {
        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        $entity = $args->getObject();
        $objectManager = $args->getObjectManager();
//if ($entity instanceof Fiscalyear) {

    }
}