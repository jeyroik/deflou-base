<?php
namespace deflou\interfaces\stages;

use deflou\interfaces\triggers\events\IApplicationEvent;
use deflou\interfaces\triggers\ITrigger;

/**
 * Interface IStageIsValidTrigger
 *
 * @package deflou\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageIsValidTrigger
{
    public const NAME = 'deflou.is.valid.trigger';

    /**
     * @param IApplicationEvent $applicationEvent
     * @param ITrigger $trigger
     * @return bool
     */
    public function __invoke(IApplicationEvent $applicationEvent, ITrigger $trigger): bool;
}
