<?php

namespace Mitirrli\Constant;

/**
 * Interface constant
 * @package Mitirrli\Queue
 */
interface constant
{
    /**
     * Queue Name
     */
    const QUEUE_NAME = 'fixed_queue_%s';

    /**
     * Lock Name
     */
    const LOCK_NAME = 'mi_lock_%s';
}
