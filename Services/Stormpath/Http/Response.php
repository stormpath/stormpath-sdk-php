<?php

interface Services_Stormpath_Http_Response extends Services_Stormpath_Http_HttpMessage
{

    public function  getHttpStatus();

    public function  isError();

    public function  isServerError();

    public function  isClientError();

}