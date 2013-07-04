<?php
/**
 *
 */

namespace Stormpath\Resource;

use Zend\Http\Client;
use Zend\Json\Json;
use Stormpath\Service\StormpathService as Stormpath;


class Account {

    private   $givenname;
    private   $surname;
    private   $password;
    private   $email;
    private   $username;
    private   $middlename;
    private   $status;

    public  function getEmail()
    {

        return $this->email;
    }

    public  function setEmail($value)
    {
        $this->email = $value;
    }

    public  function getGivenName()
    {

        return $this->givenname;
    }

    public  function setGivenName($value)
    {
        $this->givenname = $value;
    }

    public  function getSurName()
    {

        return $this->givenname;
    }

    public  function setSurName($value)
    {
        $this->surname = $value;
    }

    public  function setPassword($value)
    {
        $this->password = $value;
    }

    public  function getUserName()
    {

        return $this->username;
    }

    public  function setUserName($value)
    {
        $this->username = $value;
    }

    public  function getMiddleName()
    {

        return $this->middlename;
    }

    public  function setMiddleName($value)
    {
        $this->middlename = $value;
    }

    public  function getStatus()
    {

        return $this->status;
    }

    public  function setStatus($value)
    {
        $this->status = $value;
    }


    public  function configure($email,$givenname,$surname,$username,$status,$middlename)
    {
        $this->setEmail($email);
        $this->setGivenName($givenname);
        $this->setSurName($surname);
        $this->setUserName($username);
        $this->setMiddleName($middlename);
        $this->setStatus($status);
    }

    public  function read($accountID)
    {
        $client = Stormpath::getHttpClient();
        $client->setUri(Stormpath::BASEURI . '/accounts/' . urlencode($accountID));
        $client->setMethod('GET');

        return Json::decode($client->send()->getBody());
    }

    public  function create($directoryID)
    {
        $client = Stormpath::getHttpClient();
        $client->setUri(Stormpath::BASEURI . '/directories/' . urlencode($directoryID));
        $client->setMethod('POST');
        $client->setRawBody(Json::encode(array(
            'email' => $this->getEmail(),
            'givenName' => $this->getGivenName(),
            'surname' => $this->getSurName(),
            'username' => $this->getUserName(),
            'middleName' => $this->getMiddleName(),
            'status' => $this->getStatus(),
        )));

        return Json::decode($client->send()->getBody());
    }
}