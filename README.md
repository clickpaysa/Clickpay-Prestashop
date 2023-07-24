# ClickPay Prestashop

The official PrestaShop plugin for ClickPay
Supports PrestaShop **1.6** & **1.7**

---

## Installation

Download the compressed version from Releases page ([3.4.2](https://github.com/clickpaysa/Clickpay-Prestashop/releases/download/3.4.2/clickpay_paypage.zip))


*Then use of of the following 2 mehods:*

### Install using PrestaShop Admin panel

1. Rename the downloaded ".zip" file to **clickpay_paypage.zip**
2. Go to `Prestashop admin panel >> Improve >> Modules >> Module Manager`
3. Click on `Upload a module` then select the `clickpay_paypage.zip` file
4. Wait until the installing completes

### Install using FTP method

1. Decompress `clickpay_paypage.zip`, Then rename the folder to `clickpay_paypage`
2. Upload the folder `clickpay_paypage` to Prestashop site directory: `root/modules/`
3. Go to `Prestashop admin panel >> Improve >> Modules >> Module Catalog`
4. Search for `ClickPay`
5. Click on `Install`

---

## Activating/DeActivating the Plugin

1. Go to `Prestashop admin panel >> Improve >> Modules >> Module Manager`
2. Look for `ClickPay - PayPage` module
3. Click the `Enable` button in case the module disabled
4. From the drop down menu, Select `Disable` to disable the module

---

## Configure the Plugin

1. Go to `Prestashop admin panel >> Improve >> Modules >> Module Manager`
2. Look for `ClickPay - PayPage` module, and click `configure`
3. Navigate to the preferred payment method from the available list of ClickPay payment methods
4. Check the `Enabled` switch
5. Enter the primary credentials:
   - **Profile ID**: Enter the Profile ID of your ClickPay account
   - **Server Key**: `Merchant’s Dashboard >> Developers >> Key management >> Server Key`
6. Configure other options as your need
7. Click `Save` button *(located on bottom-right corner)*

---

## Access the Log

1. Go to `Prestashop admin panel >> Configure >> Advanced Parameters >> Logs`
2. Search for **Message**s containing `ClickPay` as this is the prefix for all ClickPay' plugin's messages

---

Done
