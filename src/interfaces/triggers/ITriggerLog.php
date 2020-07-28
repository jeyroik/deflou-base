<?php
namespace deflou\interfaces\triggers;

use extas\interfaces\IHasCreatedAt;
use extas\interfaces\IHasId;
use extas\interfaces\players\IHasPlayer;
use extas\interfaces\IItem;

/**
 * Interface ITriggerLog
 *
 * @package df\interfaces\triggers
 * @author aivanov@fix.ru
 */
interface ITriggerLog extends IItem, IHasCreatedAt, IHasId, IHasTrigger, IHasPlayer
{
    public const SUBJECT = 'deflou.trigger.log';

    public const FIELD__EVENT_ID = 'event_id';
    public const FIELD__ACTION_ID = 'action_id';
    public const FIELD__IS_SUCCESS = 'is_success';
    public const FIELD__RESPONSE_STATUS = 'response_status';
    public const FIELD__RESPONSE_BODY = 'response_body';

    public const STATUS__SUCCESS = 200;

    /**
     * @return string
     */
    public function getEventId(): string;

    /**
     * @return string
     */
    public function getActionId(): string;

    /**
     * @return int
     */
    public function getResponseStatus(): int;

    /**
     * @return string
     */
    public function getResponseBody(): string;

    /**
     * @return bool
     */
    public function isSuccess(): bool;
}
