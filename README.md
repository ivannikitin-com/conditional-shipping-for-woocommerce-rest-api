Conditional Shipping For WooCommerce REST API
=============================================
Версия 1.0.0

Расширение плагина [conditional-shipping-for-woocommerce](https://ru.wordpress.org/plugins/conditional-shipping-for-woocommerce/) методами REST API.

Необходимо для передачи правил доставки в приложения, использующие REST API.

Конечная точка
--------------
Конечной точкой является `/wp/v3/wcs_ruleset`.  
Пример URL: `https://example.com/wp-json/wp/v3/wcs_ruleset`

Авторизация пользователя
------------------------
Авторизация пользователя выполняется через пароли приложений WP и REST API ядра WP, не через ключи REST API WC!!!
Запрос возможен для пользователей с правами `read_private_posts`.
