<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once dirname(BASEPATH).'/vendor/autoload.php';

use phpseclib\Crypt\RSA;

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
	public function index()
	{
		$this->load->view('index');
	}

	public function genKey()
    {
        $tmpPath = dirname(BASEPATH).'/tmp';

        header('Content-Type: application/json');
        $rsa = new RSA();
        $rsa->setPrivateKeyFormat(RSA::PRIVATE_FORMAT_XML);
        $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_XML);
        $keypair = $rsa->createKey();
        $publickeyXML = fopen("$tmpPath/publickey.xml", 'w');
        $privatekeyXML = fopen("$tmpPath/privatekey.xml", 'w');
        $publickeyPem = fopen("$tmpPath/publickey.pem", 'w');
        $privatekeyPem = fopen("$tmpPath/privatekey.pem", 'w');
        $rsa->loadKey($keypair['privatekey']);
        fwrite($publickeyPem, $keypair['publickey']);
        fwrite($privatekeyPem, $keypair['privatekey']);
        fwrite($publickeyXML, $rsa->getPublicKey());
        fwrite($privatekeyXML, $rsa->getPrivateKey());
        echo json_encode($keypair);
    }
}
