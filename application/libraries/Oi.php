<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Oi
 *
 * Manage User messages in CodeIgniter
 *
 * @licence 	MIT Licence
 * @category	Librarys 
 * @author		Jim Wardlaw
 * @link		http://www.stucktogetherwithtape.com/code/Oi
 * @version 	1.1.0
 *
 * Changes
 * 12/07/10	- has_messages() added
 */ 
class Oi {
	
	// holds an instance of the CI librarys
	private $CI; 		

	// default class given to all notifications
	private $default_class;
	
	// default prefix given to all notification type classes
	private $class_prefix;
	
	// default alert element
	private $alert_element;

	/**
	 * Constructor
	 *
	 * Load the nessesary CI libs
	 *
	 * @access	overload
	 * @return	void
	 */	
	public function __construct()
	{
		// load an instance of the CI librarys
		$this->CI =& get_instance();
		
		// make sure session lib loaded
		if (!isset($this->CI->session))
		
			// load session library if not already loaded
			$this->CI->load->library('session');
			
		// load config file
		$this->CI->config->load('oi');
		
		// assign defaults from config file
		$this->default_class = $this->CI->config->item('default_class');		
		$this->class_prefix = $this->CI->config->item('class_prefix');		
		$this->alert_element = $this->CI->config->item('alert_element');
	}
	
	/**
	 * Call
	 *
	 * Calls the watched method.
	 *
	 * @access	overload
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function __call($method, $arguments)
	{		
		// List of watched method names
		$watched_methods = array('add_', 'get_');

		foreach ($watched_methods as $watched_method)
		{
			// See if called method is a watched method
			if (strpos($method, $watched_method) !== FALSE)
			{
				$pieces = explode($watched_method, $method);
				
				$method = $watched_method.$pieces[1];
								
				return $this->{$watched_method}(str_replace($watched_method, '', $method), $arguments);
			}
		}
				
		// show an error, for debugging's sake.
		throw new Exception("Unable to call the method \"$method\" on the class " . get_class($this));
	}


	/**
	 * returns true/false if messages exist
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function has_messages()
	{
		return $this->CI->session->userdata('oi_messages');
	}

	/**
	 * returns all messages stored in session in
	 * html format.
	 *
	 * messages are then removed from session
	 * unless a FALSE is passed as parameter
	 *
	 * @access	public
	 * @param	boolean
	 * @return	string
	 */
	public function messages($type=NULL, $keep_messages=FALSE)
	{
		// retrieve message array from session
		$messages = $this->CI->session->userdata('oi_messages');
		
		// has a message type been defined?
		if ($type)
		{
			// check messages of type exist
			if (!isset($messages[$type]))
				return;
			
			// create subset array
			$message_subset = array();
			
			// add subset to array
			$message_subset[$type] = $messages[$type];
			
			// remove subset from message array
			unset($messages[$type]);
			
			if (!$keep_messages)
				// remove subset of messages from session
				$this->CI->session->set_userdata('oi_messages', $messages);
			
			return $this->wrap_messages_in_html($message_subset);				
		}
		else
		{
			// return null if no messages exists
			if (!$messages)
				return;
				
			if (!$keep_messages)
				// remove all messages from session
				$this->CI->session->unset_userdata('oi_messages');
				
			return $this->wrap_messages_in_html($messages);
		}
	}

	/**
	 * returns messages wrapped in html
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */	
	private function wrap_messages_in_html($messages, $keep_messages=FALSE)
	{
		if (!$messages)
			return;
			
		// array to hold html
		$message_array = array();
			
		// loop through all message types in array
		foreach($messages as $type => $t_messages)
		{			
			foreach ($t_messages as $array)
			{
				// array to hold message attributes
				$attr = array();
							
				if (isset($array['attr']))
					// copy attribute array to local var
					$attr = $array['attr'];
					
				// now collate all classes to be assigned to this alert
				$classes = array();
				
				// is a default class set?
				if ($this->default_class)
					// add it to the array
					$classes[] = $this->default_class;
					
				// add notification type as a class with defined prefix
				$classes[] = $this->class_prefix.$type;
				
				// any additional classes specified in the attr array?
				if (isset($attr['class']))
					// add'um
					$classes[] = $attr['class'];
				
				// implode all classes to string
				$attr['class'] = implode(' ', $classes);
							
				// wrap alert in element and add attribute array
				$message_array[] = '<'.$this->alert_element.' '.$this->array_to_attributes($attr).'>'.$array['string'].'</'.$this->alert_element.'>';
			}
		}
						
		// return messages as string
		return implode("\n\t\t", $message_array);
	}

   /**
	* takes an associative array and converts to 
	* an HTML attribute string
	*
	* @access	private
	* @param	array
	* @return	string
	*/
	private function array_to_attributes($array)
	{
		$attributes_array = array();
	
		foreach($array as $name => $value)
		{
			$attributes_array[] = $name.'="'.$value.'"';
		}
		
		return implode(' ', $attributes_array);
	}
	
   /**
	* comment
	*
	* @access	private
	* @param	string
	* @param	array
	* @return	string
	*/
	private function add_($type, $params)
	{
		// retrieve message array from session
		$messages = $this->CI->session->userdata('oi_messages');
		
		// check if message attributes have been set
		if (isset($params[1]))
			$attr = $params[1];
		else
			$attr = NULL;
		
		// check if type exists in session array
		if (!isset($messages[$type]))
			// add new type array
			$messages[$type] = array();

		// add alert to array
		$messages[$type][] = array('string' => $params[0], 'attr' => $attr);
		
		// save session data
		$this->CI->session->set_userdata('oi_messages', $messages);		
	}

}
/* End of file oi.php */
/* Location: ./application/librarys/oi.php */