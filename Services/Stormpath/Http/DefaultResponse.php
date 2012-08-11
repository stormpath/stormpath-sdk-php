<?php

class Services_Stormpath_Http_DefaultResponse
    extends Services_Stormpath_Http_AbstractHttpMessage
    implements Services_Stormpath_Http_Response {

    private $httpStatus;
    private $headers;
    private $body;
    private $contentType;
    private $contentLength;

    public function __construct($httpStatus, $contentType, $body, $contentLength)
    {
        $this->body = $body;
        $this->contentLength = $contentLength;
        $this->contentType = $contentType;
        $this->headers = array();
        $this->httpStatus = $httpStatus;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function getContentLength()
    {
        return $this->contentLength;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function isError()
    {
        return $this->isServerError() || $this->isClientError();
    }

    public function  isServerError()
    {
        $status = $this->getHttpStatus();
        return ($status >= 500 && $status < 600);
    }

    public function isClientError()
    {
        $status = $this->getHttpStatus();
        return ($status >= 400 && $status < 500);
    }
}