Blixit Event Sourcing
=


[![Build Status](https://travis-ci.com/blixit/event-sourcing.svg?branch=master)](https://travis-ci.com/blixit/event-sourcing)
[![codecov](https://codecov.io/gh/blixit/event-sourcing/branch/master/graph/badge.svg)](https://codecov.io/gh/blixit/event-sourcing)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/blixit/event-sourcing/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/blixit/event-sourcing/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/blixit/event-sourcing/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)


This is a standalone library to create PHP7.2+ applications following the
event sourcing pattern.

### Installation

```
composer require blixit/event-sourcing
```

### Documentation

* [Basic Usage](./wiki/basic-usage.md)
* [Event Store](./wiki/event-store.md)
* [Snapshot Store](./wiki/snapshot-store.md)
* [Dispatcher and Middlewares](./wiki/dispatcher-middlewares.md)

### Why this library ?

**The Problem**

Some months ago, I heard about DDD and ES-CQRS then I was not able to stop 
reading articles about these subjects. Then I found Prooph.
 
After having been using Prooph, I came to the idea to write my own
implementation. Here are some reasons for which I gave up Prooph:

1. the documentation is really not up-to-date
2. it forces the choice of database management system (MySQL). To use, other
database management systems you have to use some libraries they develop and
that don't come with a super documentation. I'm thinking about adapters for
NoSQL Database such as MongoDb.
3. as consequence, the coupling with the infrastructure is really high
4. events stream is not easy to understand. The flux between the moment your
event is generated and its storage is not tracable
5. stream name system is not optimal and generated names are difficult to
debug. As a result, hundreds of tables can be generated into the database,
which doesn't make sense, especially for migrations handling.

I will stop here because it's not about charging against Prooph. I liked the
framework and thanks to them I got my first example of PHP implementation.

**My solution**


2&3 . To resolve the 1st problem, I used DDD and define infrastructures dependencies
as interface. For instance, the event store doesn't know anything about the
way data are really persisted. I define a `EventPersisterInterface` interface that enumerates
the relevant methods to persist or get events. By doing so, the developer can just
plug any database service behind the scene with only one constraint: respect the
interface.
The same way the `SnapshotPersisterInterface` interface tells which function are
required to persist immutable snapshots.

4 . I added some hooks to give more flexibility to the developer to interact
with the internal components of the library. For instance, I created 4 hooks
to follow the writing and the reading of an event:

- beforeWrite: when the event is ready to be added to the stream
- afterWrite : after the storage, the event persister has just been called
- beforeRead : after the database read, but just before to put the event into
the stream
- afterRead  : once the stream is built  

5 . I defined 3 naming strategies class that the developer can add
to its store: `OneStreamPerAggregateStrategy`, `SingleAggregateStreamStrategy`
`UniqueStreamStrategy`. These strategies can be switched on production. The developer
has just to move relevant events to the new stream. Notice that only the stream name
will be affected. 