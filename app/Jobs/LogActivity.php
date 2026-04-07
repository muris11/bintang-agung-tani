<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $action;

    private string $entityType;

    private ?int $entityId;

    private ?string $description;

    private ?array $metadata;

    private ?int $userId;

    private ?string $ipAddress;

    private ?string $userAgent;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?string $description = null,
        ?array $metadata = null,
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ) {
        $this->action = $action;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->description = $description;
        $this->metadata = $metadata;
        $this->userId = $userId;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ActivityLog::create([
            'user_id' => $this->userId,
            'action' => $this->action,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['activity-log', $this->action];
    }
}
