<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\SnapshotStore;

use Blixit\EventSourcing\Aggregate\AggregateRootInterface;
use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Messaging\DispatcherInterface;
use Blixit\EventSourcing\Store\EventStore;
use Blixit\EventSourcing\Store\Persistence\EventPersisterException;
use Blixit\EventSourcing\Store\Persistence\EventPersisterInterface;
use ReflectionException;
use function serialize;
use function unserialize;

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
        ?DispatcherInterface $messageBus
    ) {
        parent::__construct($aggregateClass, $eventPersister, $streamStrategyClass, $messageBus);
        $this->snapshotPersister = $snapshotPersister;

        $this->configuration = empty($configuration)
            ? new SnapshotConfiguration(self::STEP)
            : $configuration;
    }

    /**
     * @throws EventPersisterException
     * @throws ReflectionException
     */
    protected function writeLoopIteration(AggregateRootInterface &$aggregateRoot, EventInterface &$event) : void
    {
        parent::writeLoopIteration($aggregateRoot, $event);
        $snapshotAggregate = $this->buildAggregatelocally($aggregateRoot->getAggregateId());
        if ($event->getSequence() < $snapshotAggregate->getSequence() + $this->configuration->getSteps()) {
            return;
        }
        $this->snapshotPersister->snapshot($this->toSnapshot($aggregateRoot));
    }

    /**
     * @param mixed $aggregateId
     *
     * @throws ReflectionException
     */
    protected function buildAggregatelocally($aggregateId) : AggregateRootInterface
    {
        $streamName = (string) $this->getStreamNameForAggregateId($aggregateId);
        $snapshot   = $this->snapshotPersister->get($streamName);
        return $this->toAggregate($snapshot) ?? $this->getEmptyAggregate($aggregateId);
    }

    /**
     * @param mixed $aggregateId
     *
     * @throws ReflectionException
     */
    protected function buildAggregate($aggregateId) : AggregateRootInterface
    {
        return $this->buildAggregatelocally($aggregateId);
    }

    protected function toSnapshot(AggregateRootInterface $aggregateRoot) : SnapshotInterface
    {
        $streamName = (string) $this->getStreamNameForAggregateId($aggregateRoot->getAggregateId());
        return new Snapshot($streamName, serialize($aggregateRoot));
    }

    protected function toAggregate(?SnapshotInterface $snapshot = null) : ?AggregateRootInterface
    {
        return empty($snapshot)
            ? null
            : unserialize($snapshot->getPayload());
    }
}
