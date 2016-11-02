(def (greet name)
  (say-n "Hello, " name "."))
(def (think age)
  (say-n "You're so "
         (if (> age 18) "old" "young")
         "."))

(greet (ask "What is your name?"))
(think (ask "How old are you?"))