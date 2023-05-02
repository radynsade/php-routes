# radynsade/routes
Simple PHP router.

**Usage:**
```
// Manually specified method.
// '*' method will match all.
Router::route('GET', '', function (): void {
	echo 'Home';
});

// With alias function and variables.
Router::get('test/$name', function (array $params): void {
	echo 'Your name is ' . $params['name'];
});

Router::any('root/*', function (): void {
	Router::get('nested', function () {
		// Executed on /root/nested.
		echo 'Nested';
	});

	// Executed on /root
	echo 'Root';
});
```
