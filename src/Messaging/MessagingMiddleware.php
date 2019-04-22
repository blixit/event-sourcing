<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Messaging;

use function get_class;
use function is_array;
use function is_callable;

class MessagingMiddleware implements MessagingMiddlewareInterface
{
    /** To handle any message */
    protected const ALL = MessageInterface::class;

    /** @var mixed[] $handlers */
    protected $handlers;

    /**
     * @param mixed[] $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    public function handle(MessageInterface $message) : void
    {
        $class = get_class($message);
        if (! $this->supports($class)) {
            return;
        }

        foreach ($this->handlers as $key => $locatedHandler) {
            if ($key !== self::ALL && $class !== $key) {
                continue;
            }
            if (is_callable($locatedHandler)) {
                $locatedHandler($message);
                continue;
            }
            if (! is_array($locatedHandler)) {
                continue;
            }
            foreach ($locatedHandler as $handler) {
                if (! is_callable($handler)) {
                    continue;
                }
                $handler($message);
            }
        }
    }

    public function supports(string $class) : bool
    {
        return isset($this->handlers[$class]);
    }
}
