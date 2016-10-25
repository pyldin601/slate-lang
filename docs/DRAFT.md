```php
function evalApply(callable $operation, callable ...$arguments)
{
	$evaluated = eval($arguments)
	return apply($operation, ...$evaluated)
}
```
