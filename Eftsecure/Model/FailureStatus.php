<?php

namespace Callpay\Eftsecure\Model;


/**
 * Order Statuses source model
 */
class FailureStatus extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @var string
     */
    protected $_stateStatuses = [
        'canceled',
    ];
}
