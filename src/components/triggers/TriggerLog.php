<?php
namespace deflou\components\triggers;

use deflou\interfaces\triggers\ITriggerLog;
use extas\components\Item;
use extas\components\players\THasPlayer;
use extas\components\THasCreatedAt;
use extas\components\THasId;

/**
 * Class TriggerLog
 *
 * @package deflou\components\triggers
 * @author jeyroik <jeyroik@gmail.com>
 */
class TriggerLog extends Item implements ITriggerLog
{
    use THasPlayer;
    use THasTrigger;
    use THasCreatedAt;
    use THasId;

    /**
     * @return string
     */
    public function getEventId(): string
    {
        return $this->config[static::FIELD__EVENT_ID] ?? '';
    }

    /**
     * @return string
     */
    public function getActionId(): string
    {
        return $this->config[static::FIELD__ACTION_ID] ?? '';
    }

    /**
     * @return string
     */
    public function getResponseBody(): string
    {
        return $this->config[static::FIELD__RESPONSE_BODY] ?? '';
    }

    /**
     * @return int
     */
    public function getResponseStatus(): int
    {
        return $this->config[static::FIELD__RESPONSE_STATUS] ?? 0;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->getResponseStatus() == static::STATUS__SUCCESS;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
