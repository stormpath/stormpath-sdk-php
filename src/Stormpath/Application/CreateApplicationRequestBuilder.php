<?php
/**
 *
 */

namespace Stormpath\Application;

interface CreateApplicationRequestBuilder
{
    public function createDirectory();

    public function createDirectoryNamed($name);

    public function build();
}