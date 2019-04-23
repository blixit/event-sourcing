### Implementing a use case

Let say we have an aggregate Manager in charge of a project and we want
to track the actions of this manager. Let define the following actions and
their relating events:
- initializeProject : ProjectInitialized
- buildATeam : TeamBuilt
- assignTaskToWorker : TaskAssignedToWorker
- generateReports : ReportGenerated
- stopProject : ProjectStop

In this list of actions, we are going to focus on the 1st one. But
let start by defining a aggregate root:

```php
use Blixit\EventSourcing\Aggregate\AggregateRoot;

class Manager extends AggregateRoot
{
    /** @var Project $project */
    private $project;

    /**
     * @param mixed $aggregateId
     */
    public function __construct($aggregateId)
    {
        $this->setAggregateId($aggregateId);
    }
    
    // Many other functions

    public function initializeProject(InitializeProjectCommand $command) : void
    {
        $this->record(
            ProjectInitialized::occur($this->getAggregateId(), [
                    // payload with project informations
                ]
            )
        );
    }

    public function apply(EventInterface $event) : void
    {
        switch (true) {
            case $event instanceof ProjectInitialized:
                // initialize the project
                $this->project = new Project($event->getName());
                // $this->project->...
                break;
            case $event instanceof TeamBuilt:
                // set the project team
                break;
            // ...
            default:
        }
    }
}
```


let define the command (if you are using CQRS)
```php
use Blixit\EventSourcing\Command\CommandInterface;

class InitializeProjectCommand implements CommandInterface
{
    // define your command
}
```
let define the event 
```php
use Blixit\EventSourcing\Event\EventInterface;

class ProjectInitialized implements EventInterface
{
    // define your event
}
```

to go fast, you can use the default event

```php
use Blixit\EventSourcing\Event\Event;

class ProjectInitialized extends Event
{
    // define your event
}
```

Now, let connect the command and the event by creating the command handler

```php
use Blixit\EventSourcing\Store\EventStore;

class InitializeProjectHandler
{
    /** @var EventStore $eventStore */
    private $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function __invoke(InitializeProjectCommand $command) : void
    {
        /** @var Manager $manager */
        $manager = $this->eventStore->get('manager-id-123456789');

        $manager->initializeProject($command);

        $this->eventStore->store($manager);
    }
}
```

To finish, you just have to define your event handlers

```php
use Blixit\EventSourcing\Store\EventStore;

class ProjectInitializedHandler
{
    /** @var ReadModelWriter $readModelWriter */
    private $readModelWriter;

    public function __construct(ReadModelWriter $readModelWriter)
    {
        $this->readModelWriter = $readModelWriter;
    }

    public function __invoke(ProjectInitialized $event) : void
    {
        $projection = new InitializedProjectProjection();
        $projection->setProperty($event->property());

        $readModelWriter->save($projection);
    }
}
```
