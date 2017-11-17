# EFTsecure payment gateway plugin for Magento 
**Tested with Magento 2.0.x, 2.1.x and 2.2.x**

**Note: upload_1.x on a different branch in this repo**

# Description
Take instant eft payments on your Magento store using EFTsecure.

Accept ABSA, Standard Bank, Capitec, Investec, FNB and Nedbank payments directly into your bank account.

# Installation

- Copy the Eftsecure folder into the app/code folder
- In the root of the project run the following commands: 
```
php bin/magento setup:upgrade 
	
php bin/magento cache:clean
```
# Configure

- The plugin is configured in Admin under **Stores** > **Configuration**
- On the Configuration page click on the **Sales** tab then on **Payment Methods** 
- Enter Eftsecure api username and password
- Select successful payment status
- Enable payment method and save the config

