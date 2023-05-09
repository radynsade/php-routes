# radynsade/routes
Lightweight procedural routing  for PHP.

## Usage
Index page with manually specified method:
```php
Router::route('GET', '', function (): void {
	echo 'Home';
});
```
Will match all methods:
```php
Router::route('*', '', function (): void {
	echo 'Home';
});
```
Alias method with variable in the path:
```php
Router::get('test/$name', function (array $params): void {
	echo 'Your name is ' . $params['name'];
});
```
Nested routes:
```php
Router::any('root/*', function (): void {
	Router::get('nested', function () {
		// Executed on /root/nested.
		echo 'Nested';
	});

	// Executed on /root
	echo 'Root';
});
```
