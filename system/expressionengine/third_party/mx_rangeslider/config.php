<?php
if (! defined('MX_RANGESLIDER_KEY'))
{
	define('MX_RANGESLIDER_NAME', 'MX Range Slider');
	define('MX_RANGESLIDER_VER',  '1.0.1');
	define('MX_RANGESLIDER_KEY', 'mx_rangeslider');
	define('MX_RANGESLIDER_AUTHOR',  'Max Lazar');
	define('MX_RANGESLIDER_DOCS',  '');
	define('MX_RANGESLIDER_DESC',  'MX Range Slider helps you create a really nice and user friendly range select elements.');

}

/**
 * < EE 2.6.0 backward compat
 */

if ( ! function_exists('ee'))
{
    function ee()
    {
        static $EE;
        if ( ! $EE) $EE = get_instance();
        return $EE;
    }
}