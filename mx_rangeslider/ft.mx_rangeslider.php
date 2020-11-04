<?php

use MX\RangeSlider\Helper;

/**
 * MX Range Slider field type.
 *
 * @author  Max Lazar <max@eecms.dev>
 *
 * @see    https://eecms.dev/add-ons/mx-range-slider
 *
 * @copyright Copyright (c) 2020, EEC.MS
 */

/**
 * Class Mx_rangeslider_ft.
 */
class Mx_rangeslider_ft extends EE_Fieldtype
{

    public $info = array(
        'name'     => MX_RANGESLIDER_NAME,
        'version'  => MX_RANGESLIDER_VERSION
    );

    public $field2ee =  array('boolean' => 'toggle', 'number' => 'text', 'select'=>'select', 'function'=>'textarea', 'string' => 'text', 'array' => 'textarea', 'object' => 'textarea');

    private static $js_added = false;
    private static $cell_bind = true;
    private static $grid_bind = true;

    private $fallback_content = '';
    public $cell_name;
    public $has_array_data = true;

    /**
     * Package name.
     *
     * @var string
     */
    protected $package;

    /**
     * [$_themeUrl description].
     *
     * @var [type]
     */
    private static $themeUrl;


    /**
     * Field_limits_ft constructor.
     */
    public function __construct()
    {
        $this->package = basename(__DIR__);

        parent::__construct();

        if (!isset(static::$themeUrl)) {
            $themeFolderUrl = defined('URL_THIRD_THEMES') ? URL_THIRD_THEMES : ee()->config->slash_item('theme_folder_url').'third_party/';
            static::$themeUrl = $themeFolderUrl.'mx_rangeslider/';
        }
    }

    /**
     * Specify compatibility.
     *
     * @param string $name
     *
     * @return bool
     */
    public function accepts_content_type($name)
    {
        $compatibility = array(
        'low_variables',
        'channel',
        'fluid_field',
        'grid',
        );

        return in_array($name, $compatibility, false);
    }

    /**
     * Settings.
     *
     * @param array $data Existing setting data
     *
     * @return array
     */
    public function display_settings($data)
    {
        return $this->_build_settings($data);
    }

    /**
     * build_settings function.
     *
     * @param mixed $data
     */
    private function _build_settings($data, $type = false)
    {
        ee()->lang->loadfile($this->package);

        $settings = array();

        $config = self::getConfigFromFile('mx_rangeslider/Settings/RangeSliderField');

        foreach ($config as $field => $type) {

            $value = (isset($data[$field]) && '' != $data[$field]) ? $data[$field] : (false != ee()->config->item('mx_rangeslider_'.$field) ?
                ee()->config->item('mx_rangeslider_'.$field) : $config[$field]['defaults']);

           /*
            $output .= "'" . $field . "' => '" . $field . "'," . "\r\n";
            $output .= "'" . $field . "_description' => '" . $config[$field]['description'] . "'," . "\r\n";
            */

            $settings[] = array(
                        'title' => $field,
                        'desc' => $field.'_description',
                        'fields' => array(
                            'mx_rangeslider_'.$field => array(
                                'type' => $this->field2ee[$config[$field]['type']],
                                'choices' => isset($config[$field]['values']) ? $config[$field]['values'] : '',
                                'value' => $value,
                            )
                        ),
            );


        }

        return array('field_options_mx_rangeslider' => array(
                    'label' => 'field_options',
                    'group' => 'mx_rangeslider',
                    'settings' => $settings,
        ));
    }

    /**
     * Apply Config overrides to $this->settings.
     */
    private function _config_overrides()
    {
        // Check custom config values
        foreach ($this->_cfg as $key) {
            // Check the config for the value
            $val = ee()->config->item('mx_rangeslider_'.$key);

            // If not FALSE, override the settings
            if (false !== $val) {
                $this->_settings[$key] = $val;
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Check if given setting is present in the config file.
     *
     * @return bool
     */
    public function is_config($item)
    {
        return in_array($item, $this->_cfg) && (false !== ee()->config->item('mx_rangeslider_'.$item));
    }

    /**
     * Display grid settings.
     *
     * @param array $data Existing setting data
     *
     * @return array
     */
    public function grid_display_settings($data)
    {
        return $this->_build_settings($data);
    }

    /**
     * Display Low Variables settings.
     *
     * @param array $data Existing setting data
     *
     * @return array
     */
    public function var_display_settings($data)
    {
        return $this->_build_settings($data, 'lv');
    }

    /**
     * Save settings.
     *
     * @param array $data
     *
     * @return array
     */
    public function save_settings($data)
    {
       //     var_dump($data);
     //   die();

        return $this->get($data, 'mx_rangeslider');
    }

    /**
     * Save Low Variables settings.
     *
     * @param array $data
     *
     * @return array
     */
    public function var_save_settings($data)
    {
        //    var_dump(ee('Request')->post());
      //  die();

        return $this->get(ee('Request')->post(), 'mx_rangeslider');
    }

    /**
     * Displays the field in the CP.
     * @param       string      $field_name             The field name.
     * @param       array       $field_data             The previously-saved field data.
     * @param       arrray      $field_settings         The field settings.
     * @return      string      The HTML to output.
     */
    public function display_field($data, $view_type = 'field', $settings = array(), $cp = true, $passed_init = array())
    {

        $js    = "";
        $r     = "";
        $class = "";

        if (!empty($settings)) {
            $cp = false;
        }

        $cell = ($view_type != 'field') ? true : false;

        if (empty($settings)) {
            $settings = $this->settings;
        } else {
            $settings = array_merge($this->settings, $settings);
        }



        $is_grid = isset($this->settings['grid_field_id']);

        $minmax =  explode(";", $data);

        if (!empty($data) && count($minmax) > 1) {
            $this->settings['from'] = $minmax[0];
            $this->settings['to'] = $minmax[1];
        }

        $field_name = $this->field_name;

        if ($view_type == 'cell') {
            $field_name = $this->cell_name;
        }

        if ($view_type == 'cell') {
            $field_name = $this->cell_name;
            $class      .= 'mx-rangeslider-matrix';
        }

        if ($view_type == 'grid') {
            $field_name = $this->field_name;
            $class      .= 'mx-rangeslider-grid';
        }

        $pos = strpos($field_name, "[fields]");
        $fluid_field_data_id = (isset($this->settings['fluid_field_data_id'])) ? $this->settings['fluid_field_data_id'] : 0;

        if ($view_type == 'field' &&  ( $fluid_field_data_id !=0 || $pos === false )) {
            $class  .= 'mx-star-field';
        }

        $data = array(
        'name'  => $field_name,
        'id'  => str_replace(array( "[", "]" ), "_", $field_name),
        'value'  => $data
        );

        $settings = array();

        $js_block_start = '<script type="text/javascript">';
        $js_block_end   = '</script>';

        if (self::$grid_bind and $view_type == 'grid') {
            $js = ' Grid.bind("mx_rangeslider", "display", function(cell)
            {
                                var cell_obj = cell.find("input");
                                cell_obj.ionRangeSlider();
            });';

            self::$grid_bind = false;
        }

        $config = self::getConfigFromFile('mx_rangeslider/Settings/RangeSliderField');

        foreach ($config as $key => $value) {
            $attr[] = 'data-'.strtolower($key).'="'.$this->settings[$key].'"';
        };

        $attr[] = 'id="' . $data['id']. '"';
        $attr[] = 'style="display:none"';

        if (!$cell &&  ( $fluid_field_data_id !=0 || $pos === false )) {
            $js .='$("#'.$data['id'].'").ionRangeSlider();
        ';
        }

        $js .= 'FluidField.on("mx_rangeslider", "add", function(element)
        {
           var element = element.find("input");
            element.ionRangeSlider();
        });';

        if ($cp) {
            ee()->javascript->output($js);
        } else {
            $r .= $js_block_start . $js . $js_block_end;
        }

        $this->insertGlobalResources($cell);

      //  $this->insertJsCode(NL."\t".$js.NL);

        return $r.form_input($field_name, $data["value"], implode(' ', $attr));
    }

    /*
                <span class="colorpicker-custom-anchor colorpicker-circle-anchor">
                  <span class="colorpicker-circle-anchor__color" data-color></span>
                </span>
    */

    // oncolorchange:function(color) { this.style.backgroundColor = color;}
    //round-palette no-ptr palette-only flat
    //http://themesanytime.com/products/colorpicker/#docs-events

    /**
     * Display the field in a Grid cell.
     *
     * @param string $data field data
     *
     * @return string $field
     */
    public function grid_display_field($data)
    {
        return $this->display_field($data, 'grid');
    }

    /**
     * Display Low Variables field.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function var_display_field($data)
    {
        return $this->display_field($data);
    }

    /**
     * Validate field data.
     *
     * @param mixed $data Submitted field data
     *
     * @return mixed
     */
    public function validate($data)
    {
        if (!$data) {
            return true;
        }

        $errors = '';

        if ($errors) {
            return $errors;
        }

        return true;
    }

    /**
     * Validate Low Variables field.
     *
     * @param string $data
     *
     * @return mixed
     */
    public function var_save($data)
    {
        ee()->lang->loadfile('mx_rangeslider');

        $validation = $this->validate($data);

        if (true !== $validation) {
            $this->error_msg = $validation;

            return false;
        }

        return $data;
    }

    /**
     * Replace tag.
     *
     * @param string $fieldData
     * @param array  $tagParams
     *
     * @return string
     */
    public function replace_tag($data, $params = array(), $tagdata = false)
    {
        $r = '';
        $oData =  array();

        if (!$tagdata) {
            return $this->replace_value($data, $params);
        }

        $minmax =  explode(";", $data);

        $oData[0]['range_from'] = $minmax[0];
        $oData[0]['range_to'] = (count($minmax) > 1) ? $minmax[1] : $minmax[0];
        $oData[0]['range_value'] = $minmax[0];

        $r = ee()->TMPL->parse_variables($tagdata, $oData);

        return $r;
    }

    /**
     * replace_from
     * @param  [type] $data    [description]
     * @param  string $params  [description]
     * @param  string $tagdata [description]
     * @return [type]          [description]
     */
    public function replace_from($data, $params = '', $tagdata = '')
    {
        $r = '';
        $oData =  array();

        $minmax =  explode(";", $data);

        $r = $minmax[0];

        return $r;
    }

    /**
     * replace_to
     * @param  [type] $data    [description]
     * @param  string $params  [description]
     * @param  string $tagdata [description]
     * @return [type]          [description]
     */
    public function replace_to($data, $params = '', $tagdata = '')
    {
        $r = '';

        $minmax =  explode(";", $data);

        $r = (count($minmax) > 1) ? $minmax[1] : $minmax[0];

        return $r;
    }

  /**
     * replace_value function.
     *
     * @access public
     * @param mixed   $data
     * @param array   $params (default: array())
     * @return void
     */
    public function replace_value($data, $params = array())
    {
        return $data;
    }

    /**
     * Display Low Variables tag.
     *
     * @param string $fieldData
     * @param array  $tagParams
     *
     * @return string
     */
    public function var_replace_tag(
        $fieldData,
        $tagParams = array(),
        $tagData = false
    ) {
        return $this->replace_tag($fieldData, $tagParams);
    }

    /*

    HELPERS
    @needs to move to helpers file


     */

    /**
     * Insert JS in the page foot.
     *
     * @param string $js
     */
    public function insertGlobalResources($cell = false)
    {

        if (!isset(ee()->session->cache['mx_rangeslider']['header'])) {
            $this->includeJs('js/ion.rangeSlider.min.js');
            $this->includeCss('css/ion.rangeSlider.min.css');
            ee()->session->cache['mx_rangeslider']['header'] = true;
        }
    /*
        if (!isset(ee()->session->cache['mx_rangeslider']['header'])) {
        $this->includeJs('js/scripts.js');
        $this->insertJsCode(NL."\t".'var mxcpc_fields = [];'.NL);
        ee()->session->cache['mx_rangeslider']['header'] = true;
        }

        if ('grid' == $cell && !isset(ee()->session->cache['mx_rangeslider']['cell_grid'])) {
        $this->includeJs('js/mx.rangeSlider.grid.js');
        ee()->session->cache['mx_rangeslider']['cell_grid'] = true;
        }
    */
    }

    /**
 * Insert JS in the page foot.
 *
 * @param string $js
 */
    public static function insertJsCode($js)
    {
        ee()->cp->add_to_foot('<script type="text/javascript">'.$js.'</script>');
    }

    /**
     * [includeJs description].
     *
     * @param [type] $file [description]
     *
     * @return [type] [description]
     */
    public static function includeJs($file)
    {
        ee()->cp->add_to_foot('<script type="text/javascript" src="'.static::$themeUrl.$file.'"></script>');
    }

    /**
     * [includeThemeCss description].
     *
     * @param [type] $file [description]
     *
     * @return [type] [description]
     */
    public static function includeCss($file)
    {
        ee()->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.static::$themeUrl.$file.'" />');
    }

    /**
     * Settings helper.
     *
     * @param array  $data   Setting data
     * @param string $prefix
     *
     * @return array
     */
    public function get($data, $prefix)
    {
        $saveData = array();

        $prefix .= '_';

        $offset = strlen($prefix);

        foreach ($data as $saveKey => $save) {
            if (0 === strncmp($prefix, $saveKey, $offset)) {
                $saveData[substr($saveKey, $offset)] = $save;
            }
        }

        return $saveData;
    }

    /** @TODO move to helper:: */

    /**
     *
     */

    public static function getConfigFromFile(string $filePath): array
    {

        $path = PATH_THIRD  . $filePath . '.php';

        if (!file_exists($path)) {
                return [];
        }

        if (!\is_array($config = @include $path)) {
            return [];
        }

        return $config;

    }
}
