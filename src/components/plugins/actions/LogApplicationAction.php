<?php
namespace deflou\components\plugins\actions;

use deflou\components\triggers\actions\THasApplicationAction;
use deflou\components\triggers\events\THasApplicationEvent;
use deflou\components\triggers\THasTriggerObject;
use deflou\interfaces\stages\IStageAfterActionRun;
use deflou\interfaces\triggers\actions\IApplicationActionResponse;
use extas\components\plugins\Plugin;
use extas\interfaces\repositories\IRepository;

/**
 * Class LogApplicationAction
 *
 * Save application action into repository.
 *
 * @method IRepository applicationActions()
 *
 * @package deflou\components\plugins\events
 * @author jeyroik <jeyroik@gmail.com>
 */
class LogApplicationAction extends Plugin implements IStageAfterActionRun
{
    use THasTriggerObject;
    use THasApplicationEvent;
    use THasApplicationAction;

    /**
     * @param IApplicationActionResponse $response
     */
    public function __invoke(IApplicationActionResponse $response): void
    {
        $this->applicationActions()->create($this->getApplicationAction());
    }
}
