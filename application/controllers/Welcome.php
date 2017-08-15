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

	protected $tmpPath;

	public function __construct()
    {
        parent::__construct();
        $this->tmpPath = dirname(BASEPATH).'/tmp';
    }

    public function index()
	{
		$this->load->view('index');
	}

	public function genKey()
    {
        header('Content-Type: application/json');
        $rsa = new RSA();
        $rsa->setPrivateKeyFormat(RSA::PRIVATE_FORMAT_XML);
        $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_XML);
        $keypair = $rsa->createKey();
        $publickeyXML = fopen("{$this->tmpPath}/publickey.xml", 'w');
        $privatekeyXML = fopen("{$this->tmpPath}/privatekey.xml", 'w');
        $publickeyPem = fopen("{$this->tmpPath}/publickey.pem", 'w');
        $privatekeyPem = fopen("{$this->tmpPath}/privatekey.pem", 'w');
        $rsa->loadKey($keypair['privatekey']);
        fwrite($publickeyPem, $keypair['publickey']);
        fwrite($privatekeyPem, $keypair['privatekey']);
        fwrite($publickeyXML, $rsa->getPublicKey());
        fwrite($privatekeyXML, $rsa->getPrivateKey());
        echo json_encode($keypair);
    }

    public function download()
    {
        $file = isset($_GET['file']) ? $_GET['file'] : null;

        if (isset($_GET['file'])
            && file_exists("{$this->tmpPath}/$file")
            && preg_match('/[public|private]key.[xml|pem]/', $file) > 0) {
            header('Content-type: application/force-download');
            header('Content-Transfer-Encoding: Binary');
            header("Content-Disposition: attachment;filename=$file");
            echo file_get_contents("{$this->tmpPath}/$file");
        } else {
            die ("File Not Found");
        }
    }

    public function encrypt()
    {
        header('Content-Type: application/json');
        $rsa = new RSA();
        $rsa->loadKey(file_get_contents("{$this->tmpPath}/publickey.pem"));
        $plaintext = $_POST['plaintext'];
        $ciphertext = $rsa->encrypt($plaintext);
        echo json_encode(['ciphertext' => base64_encode($ciphertext)]);
    }

    public function decrypt()
    {
        header('Content-Type: application/json');
        $rsa = new RSA();
        $rsa->loadKey(file_get_contents("{$this->tmpPath}/privatekey.pem"));
        $ciphertext = base64_decode($_POST['ciphertext']);
        $plaintext = $rsa->decrypt($ciphertext);
        echo json_encode(['plaintext' => $plaintext]);
    }
}
