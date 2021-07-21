<?php
// **********************************ОПЛАТА ПО СЧЕТУ******************************* //

// Удаляем ненужные поля из платежных данных
add_filter( 'woocommerce_checkout_fields' , 'custom_checkout_fields' );
function custom_checkout_fields( $fields ) {
unset($fields['billing']['billing_postchequee']);
unset($fields['billing']['billing_state']);
unset($fields['billing']['billing_company']);
unset($fields['billing']['billing_country']);

unset($fields['billing']['billing_address_2']);

return $fields;
}
// не генерим счет для некоторых методов оплаты
function bewpi_attach_invoice_excluded_payment_methods( $payment_methods ) {
    return array( 'cod');
}
add_filter( 'bewpi_attach_invoice_excluded_payment_methods', 'bewpi_attach_invoice_excluded_payment_methods', 10, 2 );
// добавляем поля их валидацию и метаданные, если палтежный метод cheque
add_filter( 'woocommerce_gateway_description', 'gateway_cheque_appended_custom_text_fields', 10, 2 );
function gateway_cheque_appended_custom_text_fields( $description, $payment_id ){
     if( $payment_id === 'cheque' ){

        ob_start(); // Start buffering

    echo '<div class="woocommerce-organisation-fields__field-wrapper"><h3>Реквизиты организации</h3>';
    woocommerce_form_field( 'organisation_name', array(
        'required'      => true,
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'   => __('Наименование организации'),
    ), get_user_meta( $user_id, 'organisation_name', true ));

    woocommerce_form_field( 'organisation_address', array(
        'required'      => true,
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'   => __('Юридический адрес организации'),
    ), get_user_meta( $user_id, 'organisation_address', true ));            
    
    woocommerce_form_field( 'organisation_inn', array(
        'required'      => true,
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'   => __('ИИН/БИН'),
        'type'              => 'number',
    ), get_user_meta( $user_id, 'organisation_inn', true ));
    echo '</div>';

        $description .= ob_get_clean(); // Append  buffered content
    }
    return $description;
}

// цифры в счете в текст!
// Process the field (validation)
add_action('woocommerce_checkout_process', 'organisation_name_checkout_field_validation');
function organisation_name_checkout_field_validation() {
if ( $_POST['payment_method'] === 'cheque' && isset($_POST['organisation_name']) && empty($_POST['organisation_name']) )
    wc_add_notice( __( '<strong>Наименование организации</strong> является обязательным полем.' ), 'error' );
if ( $_POST['payment_method'] === 'cheque' && isset($_POST['organisation_address']) && empty($_POST['organisation_address']) )
    wc_add_notice( __( '<strong>Адрес организации</strong> является обязательным полем.' ), 'error' );
if ( $_POST['payment_method'] === 'cheque' && isset($_POST['organisation_inn']) && empty($_POST['organisation_inn']) )
    wc_add_notice( __( '<strong>ИИН/БИН</strong> является обязательным полем.' ), 'error' );  
}

// Save to the order as custom meta data
add_action('woocommerce_checkout_create_order', 'save_organisation_name_to_order_meta_data', 10, 4 );
function save_organisation_name_to_order_meta_data( $order, $data ) {
    if( $data['payment_method'] === 'cheque' && isset( $_POST['organisation_name'] ) ) {
        $order->update_meta_data( '_organisation_name', sanitize_text_field( $_POST['organisation_name'] ) );
    }
    if( $data['payment_method'] === 'cheque' && isset( $_POST['organisation_address'] ) ) {
        $order->update_meta_data( '_organisation_address', sanitize_text_field( $_POST['organisation_address'] ) );
    }
    if( $data['payment_method'] === 'cheque' && isset( $_POST['organisation_inn'] ) ) {
        $order->update_meta_data( '_organisation_inn', sanitize_text_field( $_POST['organisation_inn'] ) );
    }
}
/**
 * Возвращает сумму прописью
 * @author runcore
 * @uses morph(...)
 */
if (!function_exists('num2str')) {
function num2str($num) {
    $nul='ноль';
    $ten=array(
        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
        array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
    );
    $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
    $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
    $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
    $unit=array( // Units
        array('тиын' ,'тиын' ,'тиын',    1),
        array('тенге'   ,'тенге'   ,'тенге'    ,0),
        array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
        array('миллион' ,'миллиона','миллионов' ,0),
        array('миллиард','милиарда','миллиардов',0),
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
if (!function_exists('morph')) {
function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}
}
// чекбокс при регистрации о создании аккаунта
add_filter('woocommerce_create_account_default_checked' , function ($checked){
    return true;
});


// Updating order status to processing for orders delivered with Cheque payment methods.
add_action( 'woocommerce_thankyou', 'cheque_payment_method_order_status_to_processing', 10, 1 );
function cheque_payment_method_order_status_to_processing( $order_id ) {
    if ( ! $order_id )
        return;

    $order = wc_get_order( $order_id );


    if (  get_post_meta($order->id, '_payment_method', true) == 'cheque' )
        $order->update_status( 'processing' );
}


// текст на thankyoupage
add_filter( 'woocommerce_thankyou_order_received_text', 'misha_thank_you_title', 20, 2 );
 
function misha_thank_you_title( $thank_you_title, $order ){
    echo '<h2 style="margin-top: 0;">Уважаемый ' . $order->get_billing_first_name() . ', благодарим за Ваш Заказ!</h2>';
    echo do_shortcode( '[bewpi-download-invoice title="Загрузить счет №{formatted_invoice_number}" order_id="' . $order->get_id() . '"]' );

}
