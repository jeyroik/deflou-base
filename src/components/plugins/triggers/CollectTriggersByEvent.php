<?php
namespace deflou\components\plugins\triggers;

use deflou\interfaces\stages\IStageCollectTriggers;
use deflou\interfaces\triggers\events\IApplicationEvent;
use deflou\interfaces\triggers\ITrigger;
use extas\components\plugins\Plugin;
use extas\interfaces\repositories\IRepository;

/**
 * Class CollectTriggersByEvent
 *
 * @method IRepository triggers()
 *
 * @package deflou\components\plugins\triggers
 * @author jeyroik <jeyroik@gmail.com>
 */
class CollectTriggersByEvent extends Plugin implements IStageCollectTriggers
{
    /**
     * @param IApplicationEvent $applicationEvent
     * @param array $triggers
     */
    public function __invoke(IApplicationEvent $applicationEvent, array &$triggers): void
    {
        $triggersFound = $this->triggers()->all([ITrigger::FIELD__EVENT_NAME => $applicationEvent->getName()]);
        $triggers = array_merge($triggers, $triggersFound);
    }
}
