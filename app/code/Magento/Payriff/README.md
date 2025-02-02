# About Abhipay
Abhipay, is a service which provides the website owners to get secured online payments from the websites in a fast and simple way with the help of PCI-DSS Level 1 Service Provider adapted technical infrastructure and experienced staff and also has the licence of the Central Bank of Turkish Republic. Abhipay provides both the store’s payment security and the customer's card security with its PCI-DSS Level 1 Service Provider adapted technical infrastructure by its experienced staff.

## Abhipay - Magento 2 Virtual Pos Payment Gateway

The sellers who use Abhipay Virtual Pos payment methods add-on provide the customers to choose Abhipay payment method on the checkout page. The sellers who use Abhipay payment methods provide to take secured payments from Abhipay services. Customers using the Abhipay payment method allow them to pay securely through the store's Abhipay services. Customers can pay with multiple cards such as American Express, Visa Electron, Visa Debit, MasterCard, MasterCard Debit, etc. The module allows customers to pay with single payment or installment options. After the customer makes the payment, the amount paid is transferred to the seller's Abhipay account.

## Add-on Settings
The sellers can go to Abhipay Virtual Pos add-on by clicking the payment method link after they have logged in to Magento Admin Panel. Abhipay Virtual Pos add-on settings are done on this page. 

Copy MERCHANT ID, MERCHANT KEY values from Abhipay Merchant Panel page, then paste them on a related place in Abhipay Virtual Pos add-on. 

### How To Use Abhipay Virtual Pos Add-on To Receive Payment ###
* The customers move on to the payments step after they have added the products which they have chosen from the seller’s store to the shopping cart.
* The customers move on by choosing Abhipay Virtual Pos payment method.
* Abhipay service brings a PCI-DSS Level 1 Service Provider adapted and SSL licenced checkout page to the customer’s screen. 
* The customers complete their payments on this page by typing their card details. 
* The customers are directed to the page that will be only directed in case of the payment is succeeded after the completion of payment. 
* Abhipay service sends a notification regarding that the operation has succeeded to Abhipay Virtual Pos add-on and updates the order status. 

## Requirements
This plugin supports Magento2 version 
* 2.3.0 and higher
* 2.4.0 and higher

## Installation
You can install our plugin through Composer:
```
composer require Abhipay/magento2-payment
php bin/magento module:enable Magento_Abhipay --clear-static-content
bin/magento setup:upgrade
```
For more information see our [User Guides](https://marketplace.magento.com/media/catalog/product/Abhipay-payment-1-1-1-ce/user_guides.pdf)

## License
MIT license. For more information, see the LICENSE file.