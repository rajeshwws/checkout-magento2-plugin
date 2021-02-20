define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'ckotest',
                component: 'Rajeshwws_CKOtest/js/view/payment/method-renderer/ckotest'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
