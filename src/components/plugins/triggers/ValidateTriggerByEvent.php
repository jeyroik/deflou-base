<?php
namespace deflou\components\plugins\triggers;

use deflou\interfaces\stages\IStageIsValidTrigger;
use deflou\interfaces\triggers\events\IApplicationEvent;
use deflou\interfaces\triggers\ITrigger;
use extas\components\plugins\Plugin;

/**
 * Class ValidateTriggerByEvent
 * @package deflou\components\plugins\triggers
 * @author jeyroik <jeyroik@gmail.com>
 */
class ValidateTriggerByEvent extends Plugin implements IStageIsValidTrigger
{
    /**
     * @param IApplicationEvent $applicationEvent
     * @param ITrigger $trigger
     * @return bool
     */
    public function __invoke(IApplicationEvent $applicationEvent, ITrigger $trigger): bool
    {
        $eventParams = $applicationEvent->getArtifactsParameters();
        $triggerParams = $trigger->getEventParameters();
        foreach ($triggerParams as $triggerParam) {
            if (!isset($eventParams[$triggerParam->getName()])) {
                return false;
            }

            if (!$triggerParam->isConditionTrue($eventParams[$triggerParam->getName()])) {
                return false;
            }
        }

        return true;
    }
}
