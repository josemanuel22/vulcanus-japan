; 7DDGWSV3UJ
; Snape

(defun mi-f-ev (estado)
  (+ (* 5 (aux1-snape estado)) (* 2 (aux2-snape estado)) (aux3-snape estado)))

(defun aux1-snape (estado)
  (- (get-fichas (estado-tablero estado) (estado-lado-sgte-jugador estado) 6)
      (get-fichas (estado-tablero estado) (lado-contrario (estado-lado-sgte-jugador estado)) 6)))

(defun aux2-snape (estado)
  (- (get-fichas (estado-tablero estado) (estado-lado-sgte-jugador estado) 5)
      (get-fichas (estado-tablero estado) (lado-contrario (estado-lado-sgte-jugador estado)) 5)))


(defun aux3-snape (estado)
  (- (get-fichas (estado-tablero estado) (estado-lado-sgte-jugador estado) 0)
      (get-fichas (estado-tablero estado) (lado-contrario (estado-lado-sgte-jugador estado)) 0)))


