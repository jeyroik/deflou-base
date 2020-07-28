<?php
namespace deflou\components\plugins\events;

use deflou\interfaces\stages\IStageAfterEventEquipment;
use deflou\interfaces\triggers\events\IApplicationEvent;
use extas\components\plugins\Plugin;
use extas\interfaces\repositories\IRepository;

/**
 * Class LogApplicationEvent
 *
 * Save application event into repository.
 *
 * @method IRepository applicationEvents()
 *
 * @package deflou\components\plugins\events
 * @author jeyroik <jeyroik@gmail.com>
 */
class LogApplicationEvent extends Plugin implements IStageAfterEventEquipment
{
    /**
     * @param IApplicationEvent $applicationEvent
     * @return IApplicationEvent
     */
    public function __invoke(IApplicationEvent $applicationEvent): IApplicationEvent
    {
        $this->applicationEvents()->create($applicationEvent);

        return $applicationEvent;
    }
}
