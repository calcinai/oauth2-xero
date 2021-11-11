<?php
/**
 * @package    oauth2-xero
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace Calcinai\OAuth2\Client;


class XeroTenant
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $authEventId;

    /**
     * @var string
     */
    public $tenantId;

    /**
     * @var string
     */
    public $tenantType;

    /**
     * @var string
     */
    public $tenantName;

    /**
     * @var \DateTime
     */
    public $createdDateUtc;

    /**
     * @var \DateTime
     */
    public $updatedDateUtc;

    /**
     * @param $data
     * @return XeroTenant
     * @throws \Exception
     */
    public static function fromArray($data)
    {
        $self = new static();

        $self->id = $data['id'];
        $self->authEventId = $data['authEventId'];
        $self->tenantId = $data['tenantId'];
        $self->tenantType = $data['tenantType'];
        $self->tenantName = $data['tenantName'];
        $self->createdDateUtc = new \DateTime($data['createdDateUtc']);
        $self->updatedDateUtc = isset($data['updatedDateUtc']) ? new \DateTime($data['updatedDateUtc']) : null;

        return $self;
    }
}
