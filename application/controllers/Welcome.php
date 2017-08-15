<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	protected $tmpPath;

	public function __construct()
    {
        parent::__construct();
        $this->tmpPath = dirname(BASEPATH).'/tmp';
        $this->load->model('Key');
    }

    public function index()
	{
		$this->load->view('index');
	}

	public function genKey()
    {
        $this->Key->gen();
    }

    public function download()
    {
        $this->Key->download();
    }

    public function encrypt()
    {
        $this->Key->encrypt();
    }

    public function decrypt()
    {
        $this->Key->decrypt();
    }
}
