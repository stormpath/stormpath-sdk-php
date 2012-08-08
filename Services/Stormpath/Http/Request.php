<?php

interface Services_Stormpath_Http_Request extends Services_Stormpath_Http_HttpMessage {

    public function getMethod();

    public function getResourceUrl();

    public function getQueryString();

    public function setQueryString(array $queryString);

    public function setBody(String $body, long $length);
}