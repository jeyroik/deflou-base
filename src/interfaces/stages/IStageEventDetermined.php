<?php
namespace deflou\interfaces\stages;

use deflou\interfaces\applications\activities\IActivity;
use deflou\interfaces\applications\IApplication;
use deflou\interfaces\servers\requests\IApplicationRequest;
use extas\interfaces\http\IHasHttpIO;

/**
 * Interface IStageEventDetermined
 *
 * @package deflou\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageEventDetermined extends IHasHttpIO
{
    public const NAME = 'deflou.event.determined';

    /**
     * @param IActivity $event
     * @param IApplication $eventApp
     * @param IApplicationRequest $request
     * @return bool
     */
    public function __invoke(IActivity $event, IApplication $eventApp, IApplicationRequest &$request): bool;
}
