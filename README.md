# EFTsecure payment gateway plugin for Magento
<strong>Note: upload_1.7.x folder supports for only Magento 1.7.x</strong><br>
<strong>Note: upload_1.8.x folder supports for only Magento 1.8.x</strong><br>
<strong>Note: upload_1.9.x folder supports for only Magento 1.9.x</strong><br>
<strong>Note: upload_2.x folder supports for Magento 2.x</strong>

# Description
Take instant eft payments on your Magento store using EFTsecure.

Accept ABSA, Standard Bank, Capitec, Investec, FNB and Nedbank payments directly into your bank account.

# Installation - For Magento 1.7.x, 1.8.x, 1.9.x
<blockquote>
<p><strong>NOTE</strong> Before you begin, make a backup of your Magento site.</p>
</blockquote>

<ol>
<li>Click the Download Zip button and save to your local machine</li>
<li>Transfer the zip file to your Magento webserver</li>
<li>Unpack the archive in the root directory of your Magento instance</li>
<li>Flush your Magento caches
  <ul>
  <li>In the admin page for your Magento instance, navigate to System-&gt;Cache Management</li>
  <li>Click the 'Flush Magento Cache'</li>
  </ul>
</li>
<li>Log out of the admin page and then log back in to ensure activation of the module</li>
</ol>

# Configure - For Magento 1.7.x, 1.8.x, 1.9.x
<ul>
<li>The plugin is configured under <strong>System</strong>-&gt;<strong>Configuration</strong>-&gt;<strong>Payment Methods</strong>-&gt;<strong>EFTSecure</strong>.</li>
<li>You need to add username and password.</li>
</ul>

# Installation - For Magento 2.x
<ol>
<li>Click the Download Zip button and save to your local machine</li>
<li>Transfer the zip file to your Magento webserver</li>
<li>Unpack the archive in the root directory of your Magento instance</li>
<li>In command line, navigate to the installation directory of magento2
Enter the following commands:
<pre><code>php bin/magento setup:upgrade
php bin/magento cache:clean
</code></pre>
</li>
</ol>

# Configure - For Magento 2.x
<ol>
<li>Log into the Magento Admin</li>
<li>Go to <em>Stores</em> / <em>Configuration</em></li>
<li>Go to <em>Sales</em> / <em>Payment Methods</em></li>
<li>Scroll down to find the EFTsecure Settings</li>
<li>Enter the API Username and Password. </li>
<li>Save the settings</li>
</ol>
