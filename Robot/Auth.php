<?php namespace Robot;

use J20\Uuid\Uuid;
use Slim\Http\Cookies;

class Auth {

    private $request;
    private $encrypter;
    private $cookieDecoded;

    public function __construct($request = null, $response = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->encrypter = new Encrypter(ENCRYPT_KEY);
    }

    public function authenticate($username,$password)
    {
        $salted = md5(trim($password).AUTH_SALT);
        if(trim($username) === AUTH_UNAME && $salted  === AUTH_PWD) {
            $this->login();
            return $this->response->withRedirect('/');
        }

        return $this->response->withRedirect('/login?reason=2');
    }

    public function __invoke($request, $response, $next)
    {
        $this->request = $request;
        $this->response = $response;

        $this->encrypter = new Encrypter(ENCRYPT_KEY);

        if($this->checkSession()) {
            return $next($request, $this->response);
        }

        if($this->checkLoginFromCookie()) {
            return $next($request, $this->response);
        }

        return $this->response->withRedirect('/login?reason=1',403);
    }

    public function checkLoginFromCookie()
    {
        if(empty($this->cookieDecoded) || strlen($this->cookieDecoded) !== 36) {
            return false;
        }

        $this->login();
        return true;
    }

    public function login()
    {
        $host = $this->request->getHeader('HTTP_HOST');
        $host = reset($host);
        $newLoginCookie = Uuid::v4();

        $_SESSION['robotauth'] = $newLoginCookie;
        $cookieVars = [
            'value'    => $this->encrypter->encrypt($newLoginCookie),
            'domain'   => $host,
            'path'     => '/',
            'expires'  => time()+60*60*24*30,
            'secure'   => false,
            'httponly' => false
        ];

        $cookies = new Cookies($cookieVars);
        $cookies->set('robotcookie',$cookieVars);
        $responseCookies = $cookies->toHeaders();

        $this->response = $this->response->withAddedHeader('set-cookie', reset($responseCookies));
    }

    private function checkSession()
    {
        if(empty($_SESSION['robotauth'])) {
            return false;
        }

        $this->decodeCookie();

        if($_SESSION['robotauth'] != $this->cookieDecoded) {
            return false;
        }

        return true;
    }

    private function decodeCookie()
    {
        $cookies = $this->request->getCookieParams();
        if(empty($cookies['robotcookie'])) {
            return;
        }

        $this->cookieDecoded = $this->encrypter->decrypt($cookies['robotcookie']);
    }
}