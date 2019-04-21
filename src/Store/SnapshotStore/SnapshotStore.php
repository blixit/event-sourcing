<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\SnapshotStore;

use Blixit\EventSourcing\Aggregate\AggregateRootInterface;
use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Store\EventStore;
use Blixit\EventSourcing\Store\Persistence\EventPersisterException;
use Blixit\EventSourcing\Store\Persistence\EventPersisterInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SnapshotStore extends EventStore
{
    // define this parameter as a snapshot parameter
    public const STEP = 10;

    /** @var SnapshotPersisterInterface $snapshotPersister */
    private $snapshotPersister;

    /** @var SnapshotConfiguration $configuration */
    private $configuration;

    public function __construct(
        string $aggregateClass,
        EventPersisterInterface $eventPersister,
        string $streamStrategyClass,
        SnapshotPersisterInterface $snapshotPersister,
        ?SnapshotConfiguration $configuration,
        ?MessageBusInterface $messageBus
    ) {
        parent::__construct($aggregateClass, $eventPersister, $streamStrategyClass, $messageBus);
        $this->snapshotPersister = $snapshotPersister;

        if (! empty($configuration)) {
            return;
        }
        $this->configuration = new SnapshotConfiguration(self::STEP);
    }

    /**
     * @throws EventPersisterException
     */
    protected function writeLoopIteration(AggregateRootInterface &$aggregateRoot, EventInterface &$event) : void
    {
        parent::writeLoopIteration($aggregateRoot, $event);
        if ($event->getSequence() < $aggregateRoot->getSequence() + $this->configuration->getSteps()) {
            return;
        }
        $this->snapshotPersister->snapshot($this->toSnapshot($aggregateRoot));
    }

    /**
     * @param mixed $aggregateId
     */
    protected function buildAggregate($aggregateId) : AggregateRootInterface
    {
        $snapshot = $this->snapshotPersister->get($aggregateId);
        return $this->fromSnapshot($snapshot);
    }

    protected function toSnapshot(AggregateRootInterface $aggregateRoot) : SnapshotInterface
    {
        $snapshot = new Snapshot();
        return $snapshot;
    }

    protected function fromSnapshot(?SnapshotInterface $snapshot = null) : AggregateRootInterface
    {
        if (! empty($snapshot)) {
            return $snapshot;
        }
    }
}
