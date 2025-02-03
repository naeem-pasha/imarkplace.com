<?php

namespace Magento\Payriff\Model\Adminhtml\System\Config;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\Phrase;

/**
 * Class PayriffNotificationUrlComment
 *
 * @package Magento\Payriff\Model\Adminhtml\System\Config
 */
class PayriffNotificationUrlComment implements CommentInterface
{

    /**
     * @param  string $elementValue
     * @return Phrase|string
     */
    public function getCommentText($elementValue)
    {
        return __('Add the NOTIFICATION URL ADDRESS above to the <a href="https://www.abhipay.com.pk/magaza/ayarlar" target="_blank" rel="noopener"> Notification URL </a> setting.');
    }
}
