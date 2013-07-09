<?php

namespace Stormpath\Http;

interface HttpMessage {

	public function getHeaders();

	public function setHeaders(array $headers);

	public function hasBody();

	public function getBody();
}