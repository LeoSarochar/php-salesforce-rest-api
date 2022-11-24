<?php

namespace lsarochar\Salesforce\Authentication;

use lsarochar\Salesforce\Exception\SalesforceAuthentication;
use GuzzleHttp\Client;

class PasswordAuthentication implements AuthenticationInterface
{
    protected $client;
    protected $endPoint;
    protected $options;
    protected $access_token;
    protected $instance_url;

    public function __construct(array $options)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->endPoint = 'https://login.salesforce.com/';
        $this->options = $options;
    }

    public function authenticate()
    {
        $client = new Client();

        $request = $client->request('POST', "{$this->endPoint}/services/Soap/u/42", [
            'headers' => [
                'Content-Type' => 'text/xml',
                'SOAPAction' => '""'
            ],
            'body' => '<se:Envelope xmlns:se="http://schemas.xmlsoap.org/soap/envelope/">,
            <se:Header/>
            <se:Body>
            <login xmlns="urn:partner.soap.sforce.com">
                <username>' . $this->options['username'] . '</username>
                <password>' . $this->options['password'] . '</password>
            </login>
            </se:Body>
        </se:Envelope>'
        ]);


        $response = $request->getBody();
        if ($response) {
            $pattern = '/<sessionId>(.*)<\/sessionId>/';
            preg_match($pattern, $response, $matches);
            $this->access_token = $matches[1];
            $pattern = '/<serverUrl>(.*)<\/serverUrl>/';
            preg_match($pattern, $response, $matches);
            $this->instance_url = "https://epitech.my.salesforce.com/";

            $_SESSION['salesforce'] = [
                'access_token' => $this->access_token,
                'instance_url' => $this->instance_url
            ];
        } else {
            throw new SalesforceAuthentication($request->getBody());
        }
    }

    public function setEndpoint($endPoint)
    {
        $this->endPoint = $endPoint;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function getInstanceUrl()
    {
        return $this->instance_url;
    }
}

?>
