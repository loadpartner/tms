<?php

namespace App\Support\Events;

class TmsEventRegistry
{
    /** @var array<class-string, array<int, class-string>> */
    protected array $listeners;

    /** @var array<int, string> */
    protected array $auditableEventTypes;

    /** @var array<string, string> */
    protected array $metricsHandlers;

    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $config = config('tms_events', []);
        }

        $this->listeners = $config['listeners'] ?? [];
        $this->auditableEventTypes = $config['audit']['tracked_events'] ?? [];
        $this->metricsHandlers = $config['metrics']['handlers'] ?? [];
    }

    /**
     * @return array<class-string, array<int, class-string>>
     */
    public function listeners(): array
    {
        return $this->listeners;
    }

    public function registerListener(string $eventClass, string $listenerClass): void
    {
        $this->listeners[$eventClass] = array_values(array_unique(array_merge(
            $this->listeners[$eventClass] ?? [],
            [$listenerClass]
        )));
    }

    /**
     * @return array<int, string>
     */
    public function auditableEvents(): array
    {
        return $this->auditableEventTypes;
    }

    public function registerAuditableEvent(string $eventType): void
    {
        if (!in_array($eventType, $this->auditableEventTypes, true)) {
            $this->auditableEventTypes[] = $eventType;
        }
    }

    /**
     * @return array<string, string>
     */
    public function metricsHandlers(): array
    {
        return $this->metricsHandlers;
    }

    public function registerMetricsHandler(string $eventType, string $handlerMethod): void
    {
        $this->metricsHandlers[$eventType] = $handlerMethod;
    }

    public function getMetricsHandler(string $eventType): ?string
    {
        return $this->metricsHandlers[$eventType] ?? null;
    }
}
