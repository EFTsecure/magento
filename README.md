# EFTsecure payment gateway plugin for Magento
**Note: Tested with Magento 1.8.x or 1.9.x**

# Description
Take instant eft payments on your Magento store using EFTsecure.

Accept ABSA, Standard Bank, Capitec, Investec, FNB and Nedbank payments directly into your bank account.

# Installation - For Magento 1.x

**NOTE:** Before you begin, make a backup of your Magento site.

- Copy the Callpay_All.xml file into the app/etc/modules folder
- Copy the Callpay folder into app/code/local folder
- In the admin area navigate to **System** > **Cache Management**
- On this page **Flush Magento Cache**
- Eftsecure should then display as a payment method

# Configure

- The plugin is configured in Admin under **System** -> **Configuration** > **Payment Methods** > **EFTSecure**
- Add Eftsecure username and password and enable payment method
- Select successful order status (usually Payment Review, Processing or Complete)
- Save config