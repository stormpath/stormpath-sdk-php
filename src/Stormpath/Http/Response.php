<?php

namespace Stormpath\Http;

interface Response extends HttpMessage
{

    public function  getHttpStatus();

    public function  isError();

    public function  isServerError();

    public function  isClientError();

}