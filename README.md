# checkout-magento2-plugin
**Magento 2 plugin using the Checkout.com Unified API.<br/>**
Please follow the below step to check this functionally and you can also watch a screen recording that shows the full payment flow<br/>
Screen recording here:
![home](img/Magento2Plugin.mp4) ( attached video in email as well )<br/><br/>
**Step 1 : Create the plugin files and the Magento admin config.<br/>**
the path is : Stores->Configuration->Sales->Payment Methods - you can add the config setting here.

![home](img/configration.jpg)

**Step 2 : Code the payment form<br/>**
Payment form that will show in the Magento 2 checkout page when the user chooses “CKO test” as a payment method.
![home](img/checkout.jpg)


**Step 3: Perform the payment request<br/>**
Will need to get the token and perform a 3Ds payment request 

![home](img/payment3d.jpg)
**Step 4 GET the transaction status and display summary in the landing page.<br/>**
The transaction status and order ID should be displayed in the success page ( landing page). The transaction status should be as per the “approved” parameter in the GET payment details API call.

![home](img/Success.jpg)

**After this we can go in the admin and can see the order information will the transaction ID and payment method.<br/><br/>**
![home](img/TransactionID.jpg)
![home](img/PaymentInfo.jpg)

