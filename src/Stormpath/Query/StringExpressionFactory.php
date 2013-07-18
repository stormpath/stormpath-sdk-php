<?php
/**
 *
 */

namespace Stormpath\Query;

interface StringExpressionFactory
{
     public function eqIgnoreCase($value);

     public function startsWithIgnoreCase($value);

     public function endsWithIgnoreCase($value);

     public function containsIgnoreCase($value);
}