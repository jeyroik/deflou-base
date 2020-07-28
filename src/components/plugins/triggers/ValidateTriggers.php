<?php
namespace deflou\components\plugins\triggers;

use deflou\interfaces\stages\IStageAfterCollectTriggers;
use deflou\interfaces\stages\IStageIsValidTrigger;
use deflou\interfaces\triggers\events\IApplicationEvent;
use deflou\interfaces\triggers\ITrigger;
use extas\components\plugins\Plugin;

/**
 * Class ValidateTriggers
 *
 * @package deflou\components\plugins\triggers
 * @author jeyroik <jeyroik@gmail.com>
 */
class ValidateTriggers extends Plugin implements IStageAfterCollectTriggers
{
    /**
     * @param IApplicationEvent $applicationEvent
     * @param array $triggers
     */
    public function __invoke(IApplicationEvent $applicationEvent, array &$triggers): void
    {
        foreach ($triggers as $index => $trigger) {
            if ($this->isValidTrigger($applicationEvent, $trigger)) {
                continue;
            }

            unset($triggers[$index]);
        }
    }

    /**
     * @param IApplicationEvent $applicationEvent
     * @param ITrigger $trigger
     * @return bool
     */
    protected function isValidTrigger(IApplicationEvent $applicationEvent, ITrigger $trigger): bool
    {
        foreach ($this->getPluginsByStage(IStageIsValidTrigger::NAME) as $plugin) {
            /**
             * @var IStageIsValidTrigger $plugin
             */
            if (!$plugin($applicationEvent, $trigger)) {
                return false;
            }
        }

        return true;
    }
}
