```php

/**
 * Idea: Evaluate each argument in sequence and then apply
 *       $combinator to $results.
 */
function evalApply(callable $combinator, callable ...$f)
{
	$results = array_map(invoke(), $f)
	return $combinator(...$results)
}
```
