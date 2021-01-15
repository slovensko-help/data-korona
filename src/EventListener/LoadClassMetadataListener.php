<?php
namespace App\EventListener;

use App\Entity\Traits\Timestampable;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class LoadClassMetadataListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            'loadClassMetadata',
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $cm = $eventArgs->getClassMetadata();
        $class = $cm->getName();
        $uses = class_uses($class);

        if (in_array(Timestampable::class, $uses)) {
            $cm->table['indexes'][] = [
                'columns' => [
                    'updated_at',
                ],
            ];
        }
    }
}