<?php


class Services_Stormpath_Http_DefaultRequest
    extends Services_Stormpath_Http_AbstractHttpMessage
    implements Services_Stormpath_Http_Request
{

    private $method;
    private $resourceUrl;
    private $headers;
    private $queryString;
    private $body;
    private $contentLength;

    public function __construct(String $method,
                                String $href,
                                array $query = array(),
                                array $headers = null,
                                String $body = null,
                                long $contentLength = -1)
    {


        $this->method = $method;


        $this->queryString = $query;

        $exploded = explode('?', $href);

        if (count($exploded) == 1) {

            $this->resourceUrl = $href;

        }  else {

            $this->resourceUrl = $exploded[0];

            $query_string_str = $exploded[1];

            $explodedQuery = explode('&', $query_string_str);

            foreach($explodedQuery as $value) {

                $explodedPair = explode('=', $value);

                $this->queryString[$explodedPair[0]] = $explodedPair[1];

            }

        }

        $this->headers = $headers != null ? $headers : array();
        $this->body = $body;
        $this->contentLength = $contentLength;

    }

    public function getContentLength()
    {
        return $this->contentLength;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getQueryString()
    {
        return $this->queryString;
    }

    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }

    public function setHeaders(array $headers)
    {

        $this->headers = $headers;
    }

    public function setQueryString(array $queryString)
    {
        $this->queryString = $queryString;
    }

    public function setBody(String $body, long $length)
    {
        $this->body = $body;
        $this->contentLength = $length;
    }

}
