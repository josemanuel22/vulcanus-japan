; 7DDGWSV3UJ
; Malfoy

(defun mi-f-ev (estado)
  (+ (* 5 (aux1-malfoy estado)) (* 2 (aux2-malfoy estado)) (aux3-malfoy estado)))

(defun aux1-malfoy (estado)
  (- (get-fichas (estado-tablero estado) (estado-lado-sgte-jugador estado) 6)
      (get-fichas (estado-tablero estado) (lado-contrario (estado-lado-sgte-jugador estado)) 6)))

(defun aux2-malfoy (estado)
  (get-fichas (estado-tablero estado) (estado-lado-sgte-jugador estado) 5))

(defun aux3-malfoy (estado)
  (get-fichas (estado-tablero estado) (estado-lado-sgte-jugador estado) 0))