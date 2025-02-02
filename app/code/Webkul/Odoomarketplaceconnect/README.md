# Installation

Multi Vendor Marketplace Bridge For Magento2 module installation is very easy, please follow the steps for installation-

1. Unzip the respective extension zip and create Webkul(vendor) and Odoomarketplaceconnect(module) name folder inside your magento /app/code/ directory and then move all module's files into magento root directory Magento2/app/code/Webkul/Odoomarketplaceconnect/ folder.

    note:- while moving magento module files please ignore Odoo-Modules.zip file, cause this zip contains odoo module.

2. Run following commands via terminal
    
    php bin/magento setup:upgrade

    php bin/magento setup:di:compile

    php bin/magento setup:static-content:deploy
    

3. Flush the cache and reindex all.

now module is properly installed

# Installation of odoo modules on odoo

simply extract Odoo-Modules.zip and follow Readme.md file which is inside Odoo-Modules directory.
    
# Support

Find us our support policy - https://store.webkul.com/support.html/

# Refund

Find us our refund policy - https://store.webkul.com/refund-policy.html/
