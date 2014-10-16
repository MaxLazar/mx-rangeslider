<?php
if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

require_once PATH_THIRD . 'mx_rangeslider/config.php';

/**
 *  MX Range Slider Class for ExpressionEngine2
 *
 * @package  ExpressionEngine
 * @subpackage Fieldtypes
 * @category Fieldtypes
 * @author    Max Lazar <max@eec.ms>
 * @copyright Copyright (c) 2014 Max Lazar
 * @license
 */

class Mx_rangeslider_ft extends EE_Fieldtype
{
	/**
	 * Fieldtype Info
	 *
	 * @var array
	 */

	public $info = array(
		'name'     => MX_RANGESLIDER_NAME,
		'version'  => MX_RANGESLIDER_VER );

	// Parser Flag (preparse pairs?)
	var $has_array_data = true;

	// parameters
	 private $ft_parameters = array (
			array(
				'name' => "type",
				'type' => 'dropdown',
				'options' => array('single' => 'single','double' => 'double'),
				'default' => 'single',
				'info' => 'Optional property, will select slider type from two options: single - for single range slider, or double - for double range slider'),
			array(
				'name' => "min",
				'type' => 'input',
				'default' => '10',
				'info' => 'Optional property, automatically set from the value attribute of base input'),
			array(
				'name' => "max",
				'type' => 'input',
				'default' => '100',
				'info' => 'Optional property, automatically set from the value attribute of base input'),
			array(
				'name' => "from",
				'type' => 'input',
				'default' => '',
				'info' => 'Optional property, on default has the same value as min. overwrite default FROM setting'),
			array(
				'name' => "to",
				'type' => 'input',
				'default' => '',
				'info' => 'Optional property, on default has the same value as max. overwrite default TO setting'),
			array(
				'name' => "step",
				'type' => 'input',
				'default' => '1',
				'info' => 'Optional property, set slider step value'),
			array(
				'name' => "prefix",
				'type' => 'input',
				'default' => '',
				'info' => 'Optional property, set prefix text to all values. For example: "$" will convert "100" in to "$100"'),
			array(
				'name' => "postfix",
				'type' => 'input',
				'default' => '',
				'info' => 'Optional property, set postfix text to all values. For example: " €" will convert "100" in to "100 €"'),
			array(
				'name' => "maxPostfix",
				'type' => 'input',
				'default' => '',
				'info' => 'Optional property, set postfix text to maximum value. For example: maxPostfix - "+" will convert "100" to "100+"'),
			array(
				'name' => "hasGrid",
				'type' => 'dropdown',
				'options' => array('false' => 'false','true' => 'true'),
				'default' => 'false',
				'info' => 'Optional property, enables grid at the bottom of the slider (it adds 20px height and this can be customised through CSS)'),
			array(
				'name' => "gridMargin",
				'type' => 'input',
				'default' => '0',
				'info' => 'Optional property, enables margin between slider corner and grid'),
			array(
				'name' => "hideMinMax",
				'type' => 'dropdown',
				'options' => array('false' => 'false','true' => 'true'),
				'default' => 'false',
				'info' => 'Optional property, disables Min and Max fields.'),
			array(
				'name' => "hideFromTo",
				'type' => 'dropdown',
				'options' => array('false' => 'false','true' => 'true'),
				'default' => 'false',
				'info' => 'Optional property, disables From an To fields.'),
			array(
				'name' => "prettify",
				'type' => 'dropdown',
				'options' => array('false' => 'false','true' => 'true'),
				'default' => 'true',
				'info' => 'Optional property, allow to separate large numbers with spaces, eg. 10 000 than 10000'),
			array(
				'name' => "values",
				'type' => 'input',
				'default' => '',
				'info' => 'Array of custom values: a, b, c etc.'),
			array(
				'name' => "theme",
				'type' => 'dropdown',
				'options' => array('skinFlat' => 'skinFlat', 'skinNice' => 'skinNice', 'skinSimple' => 'skinSimple'),
				'default' => 'skinFlat',
				'info' => '')
				);

	/**
	 * PHP5 construct
	 */
	function __construct() {
		parent::__construct();
		$this->EE->lang->loadfile( MX_RANGESLIDER_KEY );
	}

	// --------------------------------------------------------------------

	/**
	 * validate function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	function validate( $data ) {
		$valid = TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * display_field function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function display_field( $data, $cell = false ) {

		$js = "";
		$field_options = array();
		$attr = array();

		$this->EE->load->helper( 'custom_field' );

		$is_grid = isset($this->settings['grid_field_id']);

		$minmax =  explode( ";", $data );

		if (!empty($data) && count($minmax) > 1) {
			$this->settings['from'] = $minmax[0];
			$this->settings['to'] = $minmax[1];
		}

		$field_name = $this->field_name;

		if (isset($this->settings['grid_field_id']))
		{
			$field_name = $this->field_name;
		}

		if ($cell == 'matrix') {
			$field_name = $this->cell_name;
		}

		$data = array(
			'name'  => $field_name,
			'id'  => str_replace( array( "[", "]" ), "_", $this->field_name ),
			'value'  => $data
		);

		foreach ($this->ft_parameters as $key => $value) {

			$attr[] = 'data-'.strtolower($value['name']).'="'.$this->settings[$value['name']].'"';
		};

		$attr[] = 'id="' . $data['id']. '"';
		$attr[] = 'style="display:none"';

		if ( !$cell )
			$js .='$("#'.$data['id'].'").ionRangeSlider();
		';

		$this->_add_js_css( $cell );

		$this->_insert_js( $js );


		return form_input( $data['name'], $data["value"], implode( ' ', $attr ));

	}

    /**
     * [grid_display_field description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
	public function grid_display_field($data)
	{
	   return $this->display_field( $data, 'grid' );
	}

    /**
     * Grid save
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
  	public function grid_save($data)
    {
        return $data;
    }

	/**
	 * Displays the cell
	 *
	 * @access public
	 * @param unknown $data The cell data
	 */
	public function display_cell( $data ) {

		return $this->display_field( $data, 'matrix' );
	}

	/**
	 * display_var_field function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function display_var_field( $data ) {

		return $this->display_field( $data, false );
	}

	/**
	 * _add_js_css function.
	 *
	 * @access private
	 * @return void
	 */
	private function _add_js_css( $cell = false ) {

		$theme_url =  $this->EE->config->item( 'theme_folder_url' ) . 'third_party/mx_rangeslider';

		if ( !isset( $this->EE->session->cache[MX_RANGESLIDER_KEY]['header'] ) ) {
			$this->EE->cp->add_to_foot( '<script type="text/javascript" src="'.$theme_url . '/js/ion.rangeSlider.min.js"></script>' );
			$this->EE->cp->add_to_foot( '<link rel="stylesheet" type="text/css" href="' .$theme_url. '/css/ion.rangeSlider.css" />' );
			$this->EE->cp->add_to_foot( '<link rel="stylesheet" type="text/css" href="' .$theme_url. '/css/normalize.min.css" />' );
			$this->EE->cp->add_to_foot( '<link rel="stylesheet" type="text/css" href="' .$theme_url. '/css/ion.rangeSlider.'.$this->settings['theme'].'.css" />' );
			$this->EE->session->cache[MX_RANGESLIDER_KEY]['header'] = true;
		};

		if ( $cell == 'matrix' && !isset( $this->EE->session->cache[MX_RANGESLIDER_KEY]['cell'] ) ) {

			$this->EE->cp->add_to_foot( '<script type="text/javascript" src="' .$theme_url. '/js/mx.rangeSlider.js"></script>' );

			$this->EE->session->cache[MX_RANGESLIDER_KEY]['cell'] = true;
		}

		if ( $cell == 'grid' && !isset( $this->EE->session->cache[MX_RANGESLIDER_KEY]['cell_grid'] ) ) {

			$this->EE->cp->add_to_foot( '<script type="text/javascript" src="' .$theme_url. '/js/mx.rangeSlider.grid.js"></script>' );

			$this->EE->session->cache[MX_RANGESLIDER_KEY]['cell_grid'] = true;
		}
	}

	/**
	 * _get_field_options function.
	 *
	 * @access private
	 * @param mixed   $data
	 * @return void
	 */
	function _get_field_options( $data ) {

		if ( ! is_array( $this->settings['options'] ) ) {
			foreach ( explode( "\n", trim( $this->settings['options'] ) ) as $v ) {
				$v = trim( $v );

				$field_options[form_prep( $v )] = form_prep( $v );
			}
		}
		else {
			$field_options = $this->settings['options'];
		}

		return $field_options;
	}

	/**
	 * _insert_js function.
	 *
	 * @access private
	 * @param mixed   $js
	 * @return void
	 */
	private function _insert_js( $js ) {
		$this->EE->cp->add_to_foot( '<script type="text/javascript">'.$js.'</script>' );
	}

	/**
	 * replace_tag function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @param string  $params  (default: '')
	 * @param string  $tagdata (default: '')
	 * @return void
	 */
	public function replace_tag( $data, $params = '', $tagdata = '' ) {

		$r = '';
        $oData =  array();

		if ( !$tagdata ) {
			return $this->replace_value( $data, $params );
		}

        $minmax =  explode( ";", $data );

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
    public function replace_from( $data, $params = '', $tagdata = '' ) {
        $r = '';
        $oData =  array();

        $minmax =  explode( ";", $data );

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
    public function replace_to( $data, $params = '', $tagdata = '' ) {
        $r = '';

        $minmax =  explode( ";", $data );

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
    public function replace_value( $data, $params = array() ) {
        return $data;
    }

	/**
	 * Display Cell Settings
	 *
	 * @access public
	 * @param unknown $cell_settings array The cell settings
	 * @return array Label and form inputs
	 */
	public function display_cell_settings( $cell_settings ) {
		return $this->_build_settings( $cell_settings );
	}

	/**
	 * display_settings function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function display_settings( $data ) {
		foreach
		( $this->_build_settings( $data ) as $v ) {
			$this->EE->table->add_row( $v );
		}
	}

    /**
     * grid_display_settings function.
     *
     * @access public
     * @param mixed   $data
     * @return void
     */
	public function grid_display_settings($data) {
		$out = array();

		foreach	( $this->_build_settings( $data ) as $v ) {
			$out[] = $this->grid_settings_row($v[0],$v[1],'');
		}

		return $out;
	}

	/**
	 * display_var_settings function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function display_var_settings( $data ) {
		return $this->_build_settings( $data, 'lv' );
	}

	/**
	 * build_settings function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	private function _build_settings( $data, $type = false ) {
		if ( $type == "lv" ) {
			$prefix = 'variable_settings['.MX_RANGESLIDER_KEY.']';
		}
		else {
			$prefix = MX_RANGESLIDER_KEY . '_';
		}

		$settings = array();

		foreach ($this->ft_parameters as $key => $value) {
			if ($value['type'] == 'dropdown') {
				$settings[] =  array( lang( $value['name'], $value['name'] ), form_dropdown( $prefix . '['.$value['name'].']', $value['options'], $this->_data_help($data, $value['name'], $value['default'])	 ) );
			};
			if ($value['type'] == 'input') {
				$settings[] = array( lang( $value['name'], $value['name'] ), form_input( $prefix . '['.$value['name'].']', $this->_data_help($data, $value['name'], $value['default']) ) );
			};
			if ($value['type'] == 'textarea') {
				$settings[] = array( lang( $value['name'], $value['name'] ), form_textarea( $prefix . '['.$value['name'].']', $this->_data_help($data, $value['name'], $value['default']) ) );
			};
		}

		//variable_settings
		return $settings;

	}

	/**
	 * _data_help function.
	 *
	 * @access private
	 * @param mixed   $data
	 * @param string  $default (default: '')
	 * @return void
	 */
	private function _data_help( $data, $key, $default = '' ) {
		return ( empty( $data[$key] ) or $data[$key] == '' ) ? $default : $data[$key];
	}

	/**
     * Grid settings validation callback; makes sure there are file upload
     * directories available before allowing a new file field to be saved
     *
     * @param   array   Grid settings
     * @return  mixed   Validation error or TRUE if passed
     */
    function grid_validate_settings($data)
    {

        return TRUE;
    }

    /**
     * [grid_save_settings description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    function grid_save_settings($data)
    {

        return $this->save_settings( $data );
    }

	/**
	 * save_cell_settings function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function save_cell_settings( $data ) {
		return $this->save_settings( $data );
	}

	/**
	 * save_var_settings function.
	 *
	 * @access public
	 * @param mixed   $var_settings
	 * @return void
	 */
	public function save_var_settings( $var_settings ) {
		return $this->save_settings( $var_settings, 'lv' );
	}

	/**
	 * save_settings function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function save_settings( $data, $type = false ) {

		$prefix = MX_RANGESLIDER_KEY . '_';


		if ( $type == "lv" )
			$data[$prefix] = $data;

		if ( isset( $data[$prefix] ) ) {

			foreach ( $data[$prefix] as $key => $val ) {

				$data[$key] = $val;

			}

		}

		return $data;

	}
	// --------------------------------------------------------------------


	// --------------------------------------------------------------------
	/**
	 * install function.
	 *
	 * @access public
	 * @return void
	 */
	public function install() {
		return array(
			'' => ''
		);

	}

	/**
	 * save function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function save( $data ) {
		return $data;
	}

	/**
	 * save_var_field function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function save_var_field( $data ) {
		return $this->save( $data );
	}

	/**
	 * save_cell function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function save_cell( $data ) {

		return $this->save( $data );

	}

	/**
     * Accept all content types.
     *
     * @param string  The name of the content type
     * @return bool   Accepts all content types
     */
    public function accepts_content_type($name)
    {
        return TRUE;
    }

}

// END mx_rangeslider_ft class

/* End of file ft.mx_rangeslider.php */
/* Location: ./expressionengine/third_party/mx_rangeslider/ft.mx_rangeslider.php */
