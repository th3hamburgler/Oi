<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Tests a number of features of the Oi notification library
	 */
	public function index()
	{
		$this->oi->add_message('Hello World this is a message');
		
		// ---
		
		$this->oi->add_success('It`s a success!');
		
		// ---
		
		$this->oi->add_warning('Just a warning...');
		
		// ---
		
		$attr = array('id' => 'error-127', 'title' => 'You really should fix this...'); 

		$this->oi->add_error('Oops, something went wrong', $attr);
		
		// ---
		
		$attr = array('class' => 'my-class'); 

		$this->oi->add_somereallystupidlylongalertname('<a href="mylink.html">Click</a> me', $attr);
		
		// ---
		
		$this->load->view('welcome_message');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */