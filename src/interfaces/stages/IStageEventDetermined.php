<?php
namespace deflou\interfaces\stages;

use deflou\interfaces\applications\activities\IActivity;
use deflou\interfaces\servers\requests\IApplicationRequest;

/**
 * Interface IStageEventDetermined
 *
 * @package deflou\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageEventDetermined
{
    public const NAME = 'deflou.event.determined';

    /**
     * @param IActivity $event
     * @param IApplicationRequest $request
     * @return bool
     */
    public function __invoke(IActivity $event, IApplicationRequest &$request): bool;
}
