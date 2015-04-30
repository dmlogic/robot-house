<?php namespace Robot;

use J20\Uuid\Uuid;
use Slim\Http\Cookies;

class Auth {

    private $session;
    private $request;
    private $response;
    private $encrypter;
    private $sessionId;
    private $browserSignature;

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
        $this->session   = new Session;
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

        // Not authenticated
        return $this->response->withRedirect('/login?reason=1',403);
    }

    /**
     * A login is a memcached browser signature against a UUID
     *
     * @return void
     */
    public function login()
    {
        $host            = $this->request->getHeader('HTTP_HOST');
        $host            = reset($host);

        $newSessionId    = Uuid::v4();
        $newSessionValue = $this->getBrowserSignature();

        $this->session->set($newSessionId,$newSessionValue);

        $cookieVars = [
            'value'    => $this->encrypter->encrypt($newSessionId),
            'domain'   => $host,
            'path'     => '/',
            'expires'  => time()+60*60*24*29,
            'secure'   => false,
            'httponly' => false
        ];

        $cookies = new Cookies($cookieVars);
        $cookies->set(AUTH_COOKIE_NAME,$cookieVars);
        $responseCookies = $cookies->toHeaders();

        $this->response = $this->response->withAddedHeader('set-cookie', reset($responseCookies));
    }

    /**
     * We'll take a very basic snapshot of the user agent and use that
     * for a session value. Simple to spoof but sufficient security for
     * this purpose.
     *
     * @return string
     */
    private function getBrowserSignature()
    {
        if(empty($this->browserSignature)) {
            $header = $this->request->getHeader('HTTP_USER_AGENT');
            $header = reset($header);
            $this->browserSignature = preg_replace('/[^a-z]/i', '',$header);
        }

        return $this->browserSignature;
    }

    /**
     * Check if we have a valid current session
     *
     * @return boolean
     */
    private function checkSession()
    {
        $this->decodeCookie();

        if(empty($this->sessionId)) {
            return false;
        }

        if($this->getBrowserSignature() !== $this->session->get($this->sessionId)) {
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

        $this->sessionId = $this->encrypter->decrypt($cookies[AUTH_COOKIE_NAME]);
    }
}