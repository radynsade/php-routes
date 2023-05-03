<?php

/**
 * Routes.
 * 
 * Copyright (c) 2023, Nikita Prokopenko radynje@gmail.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE. 
 */

namespace Radynsade\Routes;

abstract class Router {
	/**
	 * @var string
	 */
	private static string $entryPath;

	/**
	 * @var string
	 */
	private static string $requestMethod;

	/**
	 * @var string[]
	 */
	private static array $stream;

	/**
	 * @var array<string,string>
	 */
	private static array $parameters = [];

	/**
	 * @var string[]
	 */
	private static array $handled = [];

	/**
	 * Invokes automatically on class import.
	 * @return void
	 */
	public static function init(): void {
		if (!isset(self::$entryPath)) {
			self::$entryPath = trim(
				parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH),
				'/'
			);
		}

		if (!isset(self::$stream)) {
			self::$stream = self::$entryPath === ''
				? []
				: explode('/', self::$entryPath);
		}

		if (!isset(self::$requestMethod)) {
			self::$requestMethod = $_SERVER['REQUEST_METHOD'];
		}
	}

	/**
	 * @param string $method **'*'** to accept all methods.
	 * @param string $pattern
	 * @param array<string|callable> ...$handlers
	 * @return void
	 */
	public static function route(
		string $method,
		string $pattern,
		string|callable ...$handlers
	): void {
		if ($method !== '*' && $method !== self::$requestMethod) {
			return;
		}

		$strict = !str_ends_with($pattern, '/*');

		if ($pattern === '') {
			$patternParts = [];
		} else {
			$patternParts = explode('/', rtrim(trim($pattern, '/'), '/*'));
		}

		$patternLength = count($patternParts);
		$streamLength = count(self::$stream);

		if (
			($strict && $streamLength !== $patternLength)
			|| $patternLength > $streamLength
		) {
			return;
		}

		/**
		 * @var string[]
		 */
		$parametersAdded = [];

		for ($i = 0; $i < $patternLength; $i++) {
			$pathPart = array_shift(self::$stream);
			self::$handled[] = $pathPart;
			$patternPart = $patternParts[$i];

			if (str_starts_with($patternPart, '$')) {
				$parameterName = substr($patternPart, 1);
				self::$parameters[$parameterName] = $pathPart;
				$parametersAdded[] = $parameterName;
				continue;
			}

			if ($pathPart !== $patternPart) {
				$parts = array_splice(self::$handled, -$i, $i);
				array_unshift(self::$stream, ...$parts);

				foreach ($parametersAdded as $parameter) {
					unset(self::$parameters[$parameter]);
				}

				return;
			}
		}

		foreach ($handlers as $handler) {
			call_user_func($handler, self::$parameters);
		}

		exit();
	}

	public static function any(
		string $pattern,
		string|callable ...$handlers
	): void {
		self::route('*', $pattern, ...$handlers);
	}

	public static function get(
		string $pattern,
		string|callable ...$handlers
	): void {
		self::route('GET', $pattern, ...$handlers);
	}

	public static function head(
		string $pattern,
		string|callable ...$handlers
	): void {
		self::route('HEAD', $pattern, ...$handlers);
	}

	public static function post(
		string $pattern,
		string|callable ...$handlers
	): void {
		self::route('POST', $pattern, ...$handlers);
	}

	public static function put(
		string $pattern,
		string|callable ...$handlers
	): void {
		self::route('PUT', $pattern, ...$handlers);
	}

	public static function delete(
		string $pattern,
		string|callable ...$handlers
	): void {
		self::route('DELETE', $pattern, ...$handlers);
	}

	public static function connect(
		string $pattern,
		string|callable ...$handlers
	): void {
		self::route('CONNECT', $pattern, ...$handlers);
	}

	public static function options(
		string $pattern,
		string|callable ...$handlers
	): void {
		self::route('OPTIONS', $pattern, ...$handlers);
	}

	public static function trace(
		string $pattern,
		string|callable ...$handlers
	): void {
		self::route('TRACE', $pattern, ...$handlers);
	}

	public static function patch(
		string $pattern,
		string|callable ...$handlers
	): void {
		self::route('PATCH', $pattern, ...$handlers);
	}
}

Router::init();
