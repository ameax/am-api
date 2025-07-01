<?php

declare(strict_types=1);

namespace Ameax\AmApi;

use Ameax\AmApi\Config\Config;
use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Resources\AccountResource;
use Ameax\AmApi\Resources\ActionResource;
use Ameax\AmApi\Resources\AgentResource;
use Ameax\AmApi\Resources\ArticleResource;
use Ameax\AmApi\Resources\CategoryResource;
use Ameax\AmApi\Resources\CommissionResource;
use Ameax\AmApi\Resources\CustomerResource;
use Ameax\AmApi\Resources\FileResource;
use Ameax\AmApi\Resources\NoticeResource;
use Ameax\AmApi\Resources\ObjectResource;
use Ameax\AmApi\Resources\PersonResource;
use Ameax\AmApi\Resources\ProjectResource;
use Ameax\AmApi\Resources\PurposeResource;
use Ameax\AmApi\Resources\ReceiptResource;
use Ameax\AmApi\Resources\RelationResource;
use Ameax\AmApi\Resources\ReminderResource;
use Ameax\AmApi\Resources\SaleResource;
use Ameax\AmApi\Resources\TaskResource;
use Ameax\AmApi\Resources\TermResource;
use Ameax\AmApi\Resources\UserResource;

class AmApi
{
    private AmApiClient $client;

    private ?CustomerResource $customerResource = null;

    private ?AccountResource $accountResource = null;

    private ?PersonResource $personResource = null;

    private ?RelationResource $relationResource = null;

    private ?UserResource $userResource = null;

    private ?ObjectResource $objectResource = null;

    private ?ArticleResource $articleResource = null;

    private ?SaleResource $saleResource = null;

    private ?ReceiptResource $receiptResource = null;

    private ?TaskResource $taskResource = null;

    private ?ActionResource $actionResource = null;

    private ?TermResource $termResource = null;

    private ?ReminderResource $reminderResource = null;

    private ?PurposeResource $purposeResource = null;

    private ?FileResource $fileResource = null;

    private ?AgentResource $agentResource = null;

    private ?CategoryResource $categoryResource = null;

    private ?CommissionResource $commissionResource = null;

    private ?NoticeResource $noticeResource = null;

    private ?ProjectResource $projectResource = null;

    public function __construct(Config $config)
    {
        $this->client = new AmApiClient($config);
    }

    public function customers(): CustomerResource
    {
        if ($this->customerResource === null) {
            $this->customerResource = new CustomerResource($this->client);
        }

        return $this->customerResource;
    }

    public function accounts(): AccountResource
    {
        if ($this->accountResource === null) {
            $this->accountResource = new AccountResource($this->client);
        }

        return $this->accountResource;
    }

    public function persons(): PersonResource
    {
        if ($this->personResource === null) {
            $this->personResource = new PersonResource($this->client);
        }

        return $this->personResource;
    }

    public function relations(): RelationResource
    {
        if ($this->relationResource === null) {
            $this->relationResource = new RelationResource($this->client);
        }

        return $this->relationResource;
    }

    public function users(): UserResource
    {
        if ($this->userResource === null) {
            $this->userResource = new UserResource($this->client);
        }

        return $this->userResource;
    }

    public function objects(): ObjectResource
    {
        if ($this->objectResource === null) {
            $this->objectResource = new ObjectResource($this->client);
        }

        return $this->objectResource;
    }

    public function articles(): ArticleResource
    {
        if ($this->articleResource === null) {
            $this->articleResource = new ArticleResource($this->client);
        }

        return $this->articleResource;
    }

    public function sales(): SaleResource
    {
        if ($this->saleResource === null) {
            $this->saleResource = new SaleResource($this->client);
        }

        return $this->saleResource;
    }

    public function receipts(): ReceiptResource
    {
        if ($this->receiptResource === null) {
            $this->receiptResource = new ReceiptResource($this->client);
        }

        return $this->receiptResource;
    }

    public function tasks(): TaskResource
    {
        if ($this->taskResource === null) {
            $this->taskResource = new TaskResource($this->client);
        }

        return $this->taskResource;
    }

    public function actions(): ActionResource
    {
        if ($this->actionResource === null) {
            $this->actionResource = new ActionResource($this->client);
        }

        return $this->actionResource;
    }

    public function terms(): TermResource
    {
        if ($this->termResource === null) {
            $this->termResource = new TermResource($this->client);
        }

        return $this->termResource;
    }

    public function reminders(): ReminderResource
    {
        if ($this->reminderResource === null) {
            $this->reminderResource = new ReminderResource($this->client);
        }

        return $this->reminderResource;
    }

    public function purposes(): PurposeResource
    {
        if ($this->purposeResource === null) {
            $this->purposeResource = new PurposeResource($this->client);
        }

        return $this->purposeResource;
    }

    public function files(): FileResource
    {
        if ($this->fileResource === null) {
            $this->fileResource = new FileResource($this->client);
        }

        return $this->fileResource;
    }

    public function agents(): AgentResource
    {
        if ($this->agentResource === null) {
            $this->agentResource = new AgentResource($this->client);
        }

        return $this->agentResource;
    }

    public function categories(): CategoryResource
    {
        if ($this->categoryResource === null) {
            $this->categoryResource = new CategoryResource($this->client);
        }

        return $this->categoryResource;
    }

    public function commissions(): CommissionResource
    {
        if ($this->commissionResource === null) {
            $this->commissionResource = new CommissionResource($this->client);
        }

        return $this->commissionResource;
    }

    public function notices(): NoticeResource
    {
        if ($this->noticeResource === null) {
            $this->noticeResource = new NoticeResource($this->client);
        }

        return $this->noticeResource;
    }

    public function projects(): ProjectResource
    {
        if ($this->projectResource === null) {
            $this->projectResource = new ProjectResource($this->client);
        }

        return $this->projectResource;
    }

    /**
     * Get the underlying HTTP client for direct access if needed
     */
    public function getClient(): AmApiClient
    {
        return $this->client;
    }
}
