```
mul:
  - 2
  - add:
      - 2
      - 4
      - div:
          - 4
          - 9
          - 1
```

```
mul -> q
  2 -> s
add -> q
  2 -> s
  4 -> s
div -> q
  4 -> s
  9 -> s
  1 -> s

q -> apply(3)
q -> apply(3)
q -> apply(2)
```


```
mul:
  - add:
      - 2
      - div:
          - 4
          - 9
          - 1
      - 4
  - 2
```

```
mul -> q
add -> q
  2 -> s
div -> q
  4 -> s
  9 -> s
  1 -> s
q -> apply(3)
  4 -> s
q -> apply(3)
  2 -> s
q -> apply(2)
```

