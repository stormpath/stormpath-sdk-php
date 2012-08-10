<?php

interface Services_Stormpath_Http_Request extends Services_Stormpath_Http_HttpMessage {


    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_DELETE = 'DELETE';

    public function getMethod();

    public function getResourceUrl();

    public function getQueryString();

    public function setQueryString(array $queryString);

    public function setBody($body, $length);
}