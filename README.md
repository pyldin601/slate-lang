# packet-lite
[![Build Status](https://travis-ci.org/peacefulbit/packet-lite.svg?branch=master)](https://travis-ci.org/peacefulbit/packet-lite)
[![Coverage Status](https://coveralls.io/repos/github/peacefulbit/packet-lite/badge.svg?branch=master)](https://coveralls.io/github/peacefulbit/packet-lite?branch=master)

My implementation of lisp-based language interpreter written on PHP.

```
; Define constant
(def pi 3.14)

; Define function
(def (square-of-circle r) (* pi (pow r 2)))

; Call function
(print (square-of-circle 15))
```