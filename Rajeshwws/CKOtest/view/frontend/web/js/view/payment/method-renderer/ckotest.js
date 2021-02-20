define(
    [
        'jquery',
        'mage/url',
        'Magento_Checkout/js/view/payment/default'
    ],
    function ($, url, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template     : 'Rajeshwws_CKOtest/payment/form',
                ckoCardToken : null
            },

            initObservable: function () {

                this._super()
                    .observe();
                
                return this;
            },

            getCode: function() {
                return 'ckotest';
            },

            getPublicKey: function(){
                return 'pk_test_934238f0-0858-43d5-a109-f2fb18f0291a';
            },

            beforePlaceOrder : function(){
                var self = this;
                Frames.submitCard();
            },

            setCkoToken : function(token){
                this.ckoCardToken = token;
            },

            getCkoToken: function(){
                var self = this;
                return self.ckoCardToken;
            },

            initForm: function(){
                var self = this;
                var ckoPaymentForm = document.getElementById('cko-payment-form');
                //disable placeOrder button on initialization
                self.isPlaceOrderActionAllowed(false);
                //initialization
                Frames.init(self.getPublicKey());  

                /*Enable place order button if the entered details are correct*/
                Frames.addEventHandler(
                  Frames.Events.CARD_VALIDATION_CHANGED,
                  function (event) {
                    self.isPlaceOrderActionAllowed(Frames.isCardValid());
                  }
                );
                /*Set token received from checkout.com api*/
                Frames.addEventHandler(
                  Frames.Events.CARD_TOKENIZED,
                  function (event) {
                    console.log('token generated',event.token);
                    self.setCkoToken(event.token);
                    Frames.addCardToken(ckoPaymentForm,event.token);
                    //Redirect and perform 3ds payment request
                    var redirect3dUrl = url.build('ckotest/payments')+'?cko-torkn='+self.getCkoToken();
                    console.log(redirect3dUrl);
                    window.location.replace(redirect3dUrl);
                  }
                );
                
            }
        });
    }
);