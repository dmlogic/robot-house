<?php namespace Robot;

use J20\Uuid\Uuid;
use Slim\Http\Cookies;

class Auth {

    private $request;
    private $response;
    private $encrypter;
    private $cookieDecoded;

    /**
     * Make a new instance. Allow injection of HTTP objects separately from __invoke
     *
     * @param Psr\Http\Message\RequestInterface $request
     * @param Psr\Http\Message\ResponseInterface $response
     */
    public function __construct($request = null, $response = null)
    {
        $this->request   = $request;
        $this->response  = $response;

        // Will be needing this for cookies
        $this->encrypter = new Encrypter(ENCRYPT_KEY);
    }

    /**
     * Check the supplied details match. Login if they do
     *
     * @return Slim\Http\Response
     */
    public function authenticate()
    {
        $username = $this->request->getParam('username');
        $password = md5(trim($this->request->getParam('password')).AUTH_SALT);

        // A match, proceed to dashboard
        if(trim($username) === AUTH_USERNAME && $password  === AUTH_PWD) {
            $this->login();
            return $this->response->withRedirect('/');
        }

        // Boo
        return $this->response->withRedirect('/login?reason=2');
    }

    /**
     * Middleware kicks in here
     *
     * @param  Psr\Http\Message\RequestInterface $request
     * @param  Psr\Http\Message\ResponseInterface $response
     * @param  callable $next
     * @return mixed
     */
    public function __invoke($request, $response, $next)
    {
        $this->request = $request;
        $this->response = $response;

        // The session is good, carry on
        if($this->checkSession()) {
            return $next($request, $this->response);
        }

        // The cookie was good and we've logged in. Carry on
        if($this->checkLoginFromCookie()) {
            return $next($request, $this->response);
        }

        // Not authenticated
        return $this->response->withRedirect('/login?reason=1',403);
    }

    /**
     * We're going to assume that as long as a valid cookie in UUID format is
     * present, we're logged in.
     *
     * @return boolean
     */
    public function checkLoginFromCookie()
    {
        if(empty($this->cookieDecoded) || strlen($this->cookieDecoded) !== 36) {
            return false;
        }

        $this->login();
        return true;
    }

    /**
     * A login is simply a session value that matches our cookie value.
     * We set them both here.
     *
     * @return void
     */
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
        $cookies->set(AUTH_COOKIE_NAME,$cookieVars);
        $responseCookies = $cookies->toHeaders();

        $this->response = $this->response->withAddedHeader('set-cookie', reset($responseCookies));
    }

    /**
     * Check if we have a valid current session
     *
     * @return boolean
     */
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

    /**
     * Lookup our cookie value and decrypt it
     *
     * @return void
     */
    private function decodeCookie()
    {
        $cookies = $this->request->getCookieParams();
        if(empty($cookies[AUTH_COOKIE_NAME])) {
            return;
        }

        $this->cookieDecoded = $this->encrypter->decrypt($cookies[AUTH_COOKIE_NAME]);
    }
}