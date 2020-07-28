<?php
namespace deflou\components\plugins\events;

use deflou\interfaces\applications\activities\IActivity;
use deflou\interfaces\servers\requests\IApplicationRequest;
use extas\interfaces\repositories\IRepository;

/**
 * Class EventDetermineByParamEvent
 *
 * @method IRepository activities()
 *
 * @package deflou\components\plugins\events
 * @author jeyroik <jeyroik@gmail.com>
 */
class EventDetermineByParamEvent extends EventDetermine
{
    /**
     * @param IApplicationRequest $request
     * @return IActivity|null
     */
    protected function dispatch(IApplicationRequest $request): ?IActivity
    {
        $data = $request->getParameterValue($request::PARAM__DATA, []);
        $eventName = $data['event'] ?? '';

        if (!$eventName) {
            return null;
        }

        return $this->activities()->one([IActivity::FIELD__NAME => $eventName]);
    }
}
