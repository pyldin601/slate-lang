## Slate Language Specification

### Use modules inside program

Use module inside program:
```
(use math)

(def (square-of-circle r) 
  (* math.pi (math.pow r 2)))
```

Use module with name alias:
```
(use math my-math)

(def (square-of-circle r) 
  (* my-math.pi (my-math.pow r 2)))
```

### Import module functions into program

Import single function from module:
```
(import (pow) math)

(def (sum-of-squares x y)
  (+ (pow x 2) (pow y 2)))
```

Import all functions from "math" module:
```
(import math)

(def (sqrt-of-squares x y)
  (sqrt (pow x 2) (pow y 2)))
```
