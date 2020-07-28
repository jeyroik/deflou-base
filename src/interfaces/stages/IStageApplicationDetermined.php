<?php
namespace deflou\interfaces\stages;

use deflou\interfaces\applications\IApplication;
use deflou\interfaces\servers\requests\IApplicationRequest;
use extas\interfaces\http\IHasHttpIO;

/**
 * Interface IStageApplicationDetermined
 *
 * @package deflou\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageApplicationDetermined extends IHasHttpIO
{
    public const NAME = 'deflou.application.determined';

    /**
     * @param IApplication $eventApp
     * @param IApplicationRequest $request
     * @return bool
     */
    public function __invoke(IApplication $eventApp, IApplicationRequest &$request): bool;
}
