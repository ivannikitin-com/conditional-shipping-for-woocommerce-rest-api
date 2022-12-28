<?php
/**
 * Conditional Shipping For WooCommerce REST API
 *
 * @package     conditional-shipping-for-woocommerce-rest-api
 * @author      Иван Никитин
 * @copyright   2022 IvanNikitin.com
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Conditional Shipping For WooCommerce REST API
 * Plugin URI:  https://github.com/ivannikitin-com/conditional-shipping-for-woocommerce-rest-api
 * Description: Плагин добавляет функции REST API к плагину Conditional Shipping For WooCommerce
 * Version:     1.0.0
 * Author:      Иван Никитин
 * Author URI:  https://ivannikitin.com
 * Text Domain: conditional-shipping-for-woocommerce-rest-api
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
defined( 'ABSPATH' ) or die( 'Вы рыбов продаёте?' );

/**
 * Инициализация REST API
 */
add_action( 'rest_api_init', function(){
    // Добавляем маршрут 
    register_rest_route( 'wp/v3', '/wcs_ruleset', array(
        'methods'   => 'GET',
        'callback'  => 'get_conditional_shipping_for_woocommerce_rest',
        'args'      => array()
    ) );    
} );

/**
 * Функция возвращает записи wcs_ruleset
 * @param WP_REST_Request $request  Объект запроса
 * @return WP_REST_Response         Объект ответа
 */
function get_conditional_shipping_for_woocommerce_rest( $request ){
    // Авторизован ли пользователь?
    if ( ! is_user_logged_in() ){
        return new WP_Error( 'Unauthorized', 'You\'re unauthorized!', array( 'status' => 401 ) );
    }

    // Права пользователя
    if( ! current_user_can('read_private_posts' ) ){
        return new WP_Error( 'Forbidden', 'Request Forbidden!', array( 'status' => 403 ) );
    }

    // Запрос всех правил доставки
     $posts = get_posts( array(
        'post_type'     => 'wcs_ruleset',
        'post_status'   => 'publish',
        'nopaging'      => true,
        'orderby'       => 'title',
        'order'         => 'ASC'
    ) );

    // Формируем результат
    $result = array();
    foreach( $posts as $post ){
        $ruleset = array(
            'ID' => $post->ID,
            'title' => $post->post_title 
        );

        // Мета-данные CPT
        foreach ( get_metadata( 'post', $post->ID ) as $meta_name => $meta_value ){
            // Имя свойства
            $property = str_replace( '_wcs_', '',  $meta_name );

            // Значение свойства
            $value = is_array( $meta_value ) ? $meta_value[0]: $meta_value;
            switch ( $meta_name ) {
                case '_wcs_actions':
                case '_wcs_conditions':
                    $ruleset[ $property ] = unserialize( $value );
                    break;

                default:
                    $ruleset[ $property ] = $value;
            }
        }

        $result[] = $ruleset;
    }

    // Возвращаем результат
    return new WP_REST_Response( $result );
}