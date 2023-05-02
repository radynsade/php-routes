# radynsade/routes
Simple PHP router.

**Usage:**
```
	// Manually specified method.
	// '*' method will match all.
	Routes::route('GET', '', function (): void {
		echo 'Home';
	});

	// With alias function and variables.
	Routes::get('test/$name', function (array $params): void {
		echo 'Your name is ' . $params['name'];
	});

	Routes::any('root/*', function (): void {
		Routes::get('nested', function () {
			// Executed on /root/nested.
			echo 'Nested';
		});

		// Executed on /root
		echo 'Root';
	});
```
