<?php
namespace FME\Takaful\Block;

use Magento\Framework\View\Element\Template;
 
class Postform extends Template
{
        
        
        const CONFIG_CAPTCHA_PUBLIC_KEY = 'takaful/google_options/googlepublickey';
        const CONFIG_CAPTCHA_THEME = 'takaful/google_options/theme';
        const CONFIG_CAPTCHA_ENABLE = 'takaful/google_options/captchastatus';
        
        const CONFIG_FILE_EXT_UPLOAD = 'takaful/upload/allow';
        
    protected $scopeConfig;
        
        
    public function __construct(\Magento\Framework\View\Element\Template\Context $context)
    {
        
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }
        
        
               
    public function getFormAction()
    {
        return $this->getUrl('takaful/index/post', ['_secure' => true]);
    }
        
        
        
    public function getCaptchaTheme()
    {
                
        $theme = $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_THEME);
        return $theme;
    }
        
    public function isCaptchaEnable()
    {
                
        $enable = $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_ENABLE);
        return $enable;
    }
        
    public function getAllowedFileExtensions()
    {
                
        $ext = $this->scopeConfig->getValue(self::CONFIG_FILE_EXT_UPLOAD);
        return $ext;
    }
    public function getPublicKey()
    {
        
        return $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_PUBLIC_KEY);
    }
}
