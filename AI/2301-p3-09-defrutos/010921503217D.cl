; 7DDGWSV3UJ
; Hermione

(defun mi-f-ev (estado)
  (+ (* 5 (aux1-hermione estado)) (* 2 (aux2-hermione estado))))

(defun aux1-hermione (estado)
  (- (* 1 (get-fichas (estado-tablero estado) (estado-lado-sgte-jugador estado) 6))
      (* 1.2 (get-fichas (estado-tablero estado) (lado-contrario (estado-lado-sgte-jugador estado)) 6))))

(defun aux2-hermione (estado)
  (+ (get-fichas (estado-tablero estado) (estado-lado-sgte-jugador estado) 4) 
    (get-fichas (estado-tablero estado) (estado-lado-sgte-jugador estado) 5)))