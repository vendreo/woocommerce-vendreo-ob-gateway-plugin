const ven_ob_settings = window.wc.wcSettings.getSetting( 'woocommerce_vendreo_ob_gateway_data', {} );
const ven_ob_label = window.wp.htmlEntities.decodeEntities( ven_ob_settings.title ) || window.wp.i18n.__( 'Pay by Vendreo (Open Banking)', 'woocommerce_vendreo_ob_gateway' );
const ven_ob_content = () => {
    return window.wp.htmlEntities.decodeEntities( ven_ob_settings.description || '' );
};

const ven_ob_block_gateway = {
    name: 'woocommerce_vendreo_ob_gateway',
    label: ven_ob_label,
    content: Object( window.wp.element.createElement )( ven_ob_content, null ),
    edit: Object( window.wp.element.createElement )( ven_ob_content, null ),
    canMakePayment: () => true,
    ariaLabel: ven_ob_label,
    supports: {
        features: ven_ob_settings.supports,
    },
};

window.wc.wcBlocksRegistry.registerPaymentMethod( ven_ob_block_gateway );
