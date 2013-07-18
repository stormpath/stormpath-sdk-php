<?php
/**
 *
 */

namespace Stormpath\Error;

interface Error
{
    public function getStatus();

    public function getCode();

    public function getMessage();

    public function getDeveloperMessage();

    public function getMoreInfo();
}