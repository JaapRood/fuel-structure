<?php
/**
 * Structure
 *
 * Structure is a way to manage your RESTful data representations
 *
 * @package		Structure
 * @version		0.5
 * @author		Jaap Rood
 * @license		MIT License
 * @copyright	2011 Jaap Rood
 * @link		http://github.com/JaapRood/fuel-structure
 */

namespace Structure;

class Structure {
    
    /**
     * @var mixed   data to structurize
     */
    protected $_data = array();
    
    /**
     * Returns an instance of the Structure object
     *
     * @param	mixed	$data	data to be structurized
     */
    public static function factory($data = null) {
        return new static($data);
    }
    
    /**
     * constructor
     *
     * call the static factory method to create new objects
     *
     * @param	mixed	$data	data to be structurized
     */
    public function __construct($data = null) {
		$load = \Config::load('structure', true);
		
        $this->_data = $data;
    }
    
	/**
	 * passes the data to the template and returns the result
	 *
	 * @param 	string 	$template_file	the template file used to structure the data
	 * @return 	array	the restructured data
	 */
	public function to($template) {
		$template_folder = \Config::get('structure.templates_folder');
		
		$template_path = \Fuel::find_file($template_folder, $template);
		
		if (!$template_path) {
			throw new \Fuel_Exception('The requested template could not be found: '. \Fuel::clean_path($file));
		}
		
		$data = static::capture($template_path, $this->_data);

		return static::capture($template_path, $this->_data);
	}
	
	
	/**
	 * Capture the restructured data when the template file is included. The data will be
	 * extracted into the local scope, which is why this is a static function; this way the
	 * template file can't access the object scope.
	 *
	 * @param	string	$_template_file	the file that contains the logic to restructure the data
	 * @param	string	$_template_data the data that needs to be restructured
	 * @return 	array	the restructured data
	 */
	protected static function capture($_template_file, array $_template_data) {
		$_template_data AND extract($_template_data, EXTR_SKIP); // we should never risk stability of the system by overwriting stuff we don't want to
		
		try { // make sure we don't break the app unnecessarily
			// load the template within the scope
			return include $_template_file;
			
		} catch (\Exception $e) {
			throw new \Fuel_Exception($e->getMessage());
		}
	}
	
	/**
	 * convert ORM models to arrays and unindex their arrays in order to create well formed
	 * native JSON
	 *
	 * @param	mixed	$data	data that needs Orm Models converted to arrays
	 */
	public static function models_to_array($data) {
		if (is_array($data)) { // if it's an array
			if (array_shift(array_values($data)) instanceof \Orm\Model) { // if the first element is a Orm Model
				$new_data = array();
				
				foreach ($data as $model) {
					
					$converted_properties = array();
					foreach($model as $key => $property) { // for each property
						$converted_properties[$key] = static::models_to_array($property); // could be an array of models
					}
					
					$converted_model = $model->to_array();
					
					foreach ($converted_properties as $key => $property) {
						$converted_model[$key] = $property;
					}
					
					$new_data[] = $converted_model;
				}
				
				$data = $new_data;
			}
		} elseif ($data instanceof \Orm\Model) {
			$converted_properties = array();
			foreach($data as $key => $property) { // for each property
				$converted_properties[$key] = static::models_to_array($property); // could be an array of models
			}
			
			$converted_model = $data->to_array();
			
			foreach ($converted_properties as $key => $property) {
				$converted_model[$key] = $property;
			}
			
			$data = $converted_model;
		}
		
		return $data;
	}
}