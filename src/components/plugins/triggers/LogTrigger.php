<?php
namespace deflou\components\plugins\triggers;

use deflou\components\triggers\actions\THasApplicationAction;
use deflou\components\triggers\events\THasApplicationEvent;
use deflou\components\triggers\THasTriggerObject;
use deflou\components\triggers\TriggerLog;
use deflou\interfaces\stages\IStageAfterActionRun;
use deflou\interfaces\triggers\actions\IApplicationActionResponse;

use extas\components\plugins\Plugin;
use extas\interfaces\repositories\IRepository;
use Ramsey\Uuid\Uuid;

/**
 * Class LogTrigger
 *
 * Save application action into repository.
 *
 * @method IRepository triggersLogs()
 *
 * @package deflou\components\plugins\triggers
 * @author jeyroik <jeyroik@gmail.com>
 */
class LogTrigger extends Plugin implements IStageAfterActionRun
{
    use THasTriggerObject;
    use THasApplicationEvent;
    use THasApplicationAction;

    /**
     * @param IApplicationActionResponse $response
     * @return IApplicationActionResponse
     */
    public function __invoke(IApplicationActionResponse $response): IApplicationActionResponse
    {
        $this->triggersLogs()->create(new TriggerLog([
            TriggerLog::FIELD__EVENT_ID => $this->getApplicationEvent()->getId(),
            TriggerLog::FIELD__ACTION_ID => $this->getApplicationAction()->getId(),
            TriggerLog::FIELD__TRIGGER_NAME => $this->getTrigger()->getName(),
            TriggerLog::FIELD__RESPONSE_BODY => $response->getBody(),
            TriggerLog::FIELD__RESPONSE_STATUS => $response->getStatus(),
            TriggerLog::FIELD__CREATED_AT => time(),
            TriggerLog::FIELD__ID => Uuid::uuid6()->toString()
        ]));

        return $response;
    }
}
