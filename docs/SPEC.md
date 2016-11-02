## Slate Language Specification

### Use modules inside program

Use module inside program:
```
; Syntax: (use [module-name])
(use math)

(def (square-of-circle r) 
  (* math.pi (math.pow r 2)))
```

Use module with name alias:
```
; Syntax: (use [module-name] [module-alias])
(use math my-math)

(def (square-of-circle r) 
  (* my-math.pi (my-math.pow r 2)))
```

### Import module functions into program

Import single or multiple functions from module:
```
; Syntax: (import ([list-of-functions]) [module-name])
(import (pow) math)

(def (sum-of-squares x y)
  (+ (pow x 2) (pow y 2)))
```

Import all functions from module:
```
; Syntax: (import [module-name])
(import math)

(def (sqrt-of-squares x y)
  (sqrt (pow x 2) (pow y 2)))
```
