<?php
namespace Meezan\MeezanBank\Plugin;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Store\Model\ScopeInterface;
use Meezan\MeezanBank\Validator\SameSite;
class SwitchSameSite
{
    const CONFIG_AFFECTED_KEYS = 'web/cookie/affected_keys';
    private $validator;
    private $header;
    private $scopeConfig;
    private $affectedKeys = [];
    public function __construct(
        Header $header,
        ScopeConfigInterface $scopeConfig,
        SameSite $validator
    ) {
        $this->validator = $validator;
        $this->header = $header;
        $this->scopeConfig = $scopeConfig;
    }
    public function beforeSetPublicCookie(
        PhpCookieManager $subject,
        $name,
        $value,
        PublicCookieMetadata $metadata = null
    ) {
        if ($this->isAffectedKeys($name)) {
            $agent = $this->header->getHttpUserAgent();
            $sameSite = $this->validator->shouldSendSameSiteNone($agent);
            if ($sameSite === false) {
                $metadata
                    ->setSecure(true)
                    ->setSameSite('None');
            } else {
                $config = 'None';
                // Convert to lowercase since sometimes it comes as lower-cased string
                if(strtolower($config) === 'none')
                {
                    $metadata->setSecure(true);
                }
                $metadata->setSameSite($config);
            }
        }
        return [$name, $value, $metadata];
    }
    private function isAffectedKeys($name)
    {
        if (!count($this->affectedKeys)) {
            $affectedKeys = $this->scopeConfig->getValue(self::CONFIG_AFFECTED_KEYS, ScopeInterface::SCOPE_STORE);
            $this->affectedKeys = explode(',', strtolower($affectedKeys));
        }

        return in_array(strtolower($name), $this->affectedKeys);
    }
}