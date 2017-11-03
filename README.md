# EFTsecure payment gateway plugin for Magento
**Note: upload_1.x folder supports for Magento 1.8.x or 1.9.x**
**Note: upload_2.x folder supports for Magento 2.0.x, 2.1.x and 2.2.x**

# Description
Take instant eft payments on your Magento store using EFTsecure.

Accept ABSA, Standard Bank, Capitec, Investec, FNB and Nedbank payments directly into your bank account.

# Installation - For Magento 1.x

**NOTE:** Before you begin, make a backup of your Magento site.

- Copy the Callpay_All.xml file into the app/etc/modules folder
- Copy the Callpay folder into app/code/local folder
- In the admin area navigate(click) on menu item System->Cache Management
- On this page click 'Flush Magento Cache'
- Eftsecure should then display as a payment method

# Configure - For Magento 1.x

- The plugin is configured in Admin under **System** -> **Configuration** -> **Payment Methods** -> **EFTSecure**
- Add Eftsecure username and password and enable payment method

# Installation - For Magento 2.x

- Copy the Eftsecure folder into the app/code folder
- In the root of the project run the following commands: 

    php bin/magento setup:upgrade 
	php bin/magento cache:clean

# Configure - For Magento 2.x

- The plugin is configured in Admin under **Stores** -> **Configuration**
- On the Configuration page click on the **Sales** tab then on **Payment Methods** 
- Add Eftsecure username and password and enable payment method
- Save config