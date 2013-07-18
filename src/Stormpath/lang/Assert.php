<?php
/**
 *
 */

abstract class Assert
{
	public static function isTrue($expression, $message)
	{
		if (!$expression && !empty($message)) {
			throw new InvalidArgumentException($message);
		}
		elseif (!$expression && empty($message)) {
			throw new InvalidArgumentException('[Assertion failed] - this expression must be true');
		}
	}
	public static function isNull($object, $message = "")
	{
		if ($object != null && !empty($message)) {
			throw new InvalidArgumentException($message);
		}
		elseif ($object != null && empty($message)) {
			throw new InvalidArgumentException('[Assertion failed] - the object argument must be null');
		}
	}

	public static function notNull($object, $message = "")
	{
		if ($object == null && !empty($message)) {
			throw new InvalidArgumentException($message);
		}
		elseif ($object == null && empty($message))
			throw new InvalidArgumentException('[Assertion failed] - this argument is required; it must not be null');
	}

	public static function hasLength($text, $message = "")
	{
		if (strlen($text) == 0  && !empty($message)) {
			throw new InvalidArgumentException($message);
		}

		elseif (strlen($text) == 0  && empty($message)) {
			throw new InvalidArgumentException('[Assertion failed] - this String argument must have length; it must not be null or empty');
		}
	}

	public static function hasText($text, $message = "")
	{
		if (empty($text) == 0  && !empty($message)) {
			throw new InvalidArgumentException($message);
		}

		elseif (empty($text) == 0  && empty($message)) {
			throw new InvalidArgumentException('[Assertion failed] - this String argument must have text; it must not be null, empty, or blank');
		}
	}

	public static function doesNotContain($textToSearch, $substring, $message = "")
	{
		if ((strlen($textToSearch) == 0 || $textToSearch == null) &&
			(strlen($substring) == 0 ||  $substring == null) &&
			(substr($substring,-1) == false) && (!empty($message))) {
			throw new InvalidArgumentException($message);
		}

		elseif ((strlen($textToSearch) == 0 || $textToSearch == null) &&
			(strlen($substring) == 0 ||  $substring == null) &&
			(substr($substring,-1) == false) && (empty($message))) {
			throw new InvalidArgumentException("[Assertion failed] - this String argument must not contain the substring [".$substring."]");
		}
	}

	public static function notEmpty($array = array(), $message = "")
	{
		if (empty($array) && !empty($message)) {
			throw new InvalidArgumentException($message);
		}
		elseif (empty($array) && empty($message)) {
			throw new InvalidArgumentException('[Assertion failed] - this array must not be empty: it must contain at least 1 element');
		}
	}

	public static function noNullElements($array = array(), $message = "")
	{
			if ($array != null) {
				for ($i = 0; $i < count($array); $i++) {
					if ($array[$i] == null && !empty($message)) {
						throw new InvalidArgumentException($message);
					}
					elseif ($array[$i] == null && empty($message)) {
						throw new InvalidArgumentException('[Assertion failed] - this array must not contain any null elements');
					}
				}
			}
	}

	public static function isAssignable(stdClass $superType, stdClass $subType, $message = "")
	{
		self::notNull($superType, "Type to check against must not be null");
		if ($subType == null || ($superType !== $subType)) {
			throw new InvalidArgumentException($message);
		}
	}

	public static function state($expression, $message)
	{
		if (!$expression && !empty($message)) {
			throw new InvalidArgumentException($message);
		}
		elseif (!$expression && empty($message)) {
			throw new InvalidArgumentException("[Assertion failed] - this state invariant must be true");
		}
	}

}