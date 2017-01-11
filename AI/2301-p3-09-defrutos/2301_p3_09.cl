;;; ------------------------------------------------------------------------------------------
;;; Implementación del algoritmo minimax con poda alfa-beta
;;; RECIBE:   estado, profundidad actual, beta, alfa,
;;;           devolver-movimiento: flag que indica si devolver un estado (llamada raiz) o un valor numérico (resto de llamadas)
;;;           profundidad-max    : límite de profundidad
;;;           f-eval             : función de evaluación
;;; DEVUELVE: valor minimax en todos los niveles de profundidad, excepto en el nivel 0 que devuelve el estado del juego tras el
;;;           movimiento que escoge realizar
;;; ------------------------------------------------------------------------------------------

(defun minimax-a-b (estado profundidad-max f-eval)
    (let* ((oldverb *verb*) ; Si la bandera de debug esta puesta modo verboso en la ejecucion de minimax. Si no modo vervoso desactivado
         (*verb* (if *debug-mmx* *verb* nil))
         (estado2 (minimax-1-a-b estado 0 t profundidad-max -99999 99999 f-eval)) ; Llamada a minimax desde la profundidad inicial 0 (modo verboso o no). Es decir empezamos la busqueda como tal.
         (*verb* oldverb)) ;Restabecemos el valor de *verb*.
    estado2))



 (defun minimax-1-a-b (estado profundidad devolver-movimiento profundidad-max alfa beta f-eval)
  (cond ((>= profundidad profundidad-max) ;En caso de llegar a la profundidad maxima estabelcida. Paramos la busqueda y evaluamos el tablero.
         (unless devolver-movimiento  (funcall f-eval estado))) ;Si la bandera devolver-movimiento esta a nil devolvemos el valor del estado del tablero segun nuestra heuristica.
         (t
         (let* ((sucesores (generar-sucesores estado))
                (mejor-sucesor nil))
           (cond ((null sucesores) ;Si ya no quedan sucecesores en la lista hemos llegado al final de nuestra exploracion y evaluamos el estado.
                  (unless devolver-movimiento  (funcall f-eval estado)))
                  (t
                  (loop for sucesor in sucesores do     ;iteramos por cada sucesor generado
                    (let* ((resultado-sucesor (minimax-1-a-b sucesor (1+ profundidad)  ;Calculamos su valor minimax
                                        nil profundidad-max (- beta) (- alfa) f-eval))
                           (valor-nuevo (- resultado-sucesor)))   ;Y lo negamos (negamax)
                      ;(format t "~% Mmx-1 Prof:~A valor-nuevo ~4A de sucesor  ~A" profundidad valor-nuevo (estado-tablero sucesor))
                      (when (> valor-nuevo alfa)  ;Actualizamos el alfa al ser nodo max (todos lo son)
                        (setq alfa valor-nuevo)
                        (setq mejor-sucesor  sucesor))
                      (when ( <= beta alfa)        ;si alfa es mayor que beta, podamos (no exploramos las demas ramas de ese nodo)
                        (return (if  devolver-movimiento mejor-sucesor alfa)))))
                  (if  devolver-movimiento mejor-sucesor alfa)))))))


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;; Otras versiones minimax para calcular tiempos segun ordenacion
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;
;;; minimax-a-b-sort
;;;
;;; minimax con poda pero que ademas ordena de mayor a menor valor heuristico los sucesores.
;;;
(defun minimax-a-b-sort (estado profundidad-max f-eval)
    (let* ((oldverb *verb*) ; Si la bandera de debug esta puesta modo verboso en la ejecucion de minimax. Si no modo vervoso desactivado
         (*verb* (if *debug-mmx* *verb* nil))
         (estado2 (minimax-1-a-b-sort estado 0 t profundidad-max -99999 99999 f-eval)) ; Llamada a minimax desde la profundidad inicial 0 (modo verboso o no). Es decir empezamos la busqueda como tal.
         (*verb* oldverb)) ;Restabecemos el valor de *verb*.
    estado2))



 (defun minimax-1-a-b-sort (estado profundidad devolver-movimiento profundidad-max alfa beta f-eval)
  (cond ((>= profundidad profundidad-max) ;En caso de llegar a la profundidad maxima estabelcida. Paramos la busqueda y evaluamos el tablero.
         (unless devolver-movimiento  (funcall f-eval estado))) ;Si la bandera devolver-movimiento esta a nil devolvemos el valor del estado del tablero segun nuestra heuristica.
         (t
         (let* ((sucesores (sort (generar-sucesores estado) #'< :key f-eval))
                (mejor-sucesor nil))
           (cond ((null sucesores) ;Si ya no quedan sucecesores en la lista hemos llegado al final de nuestra exploracion y evaluamos el estado.
                  (unless devolver-movimiento  (funcall f-eval estado)))
                  (t
                  (loop for sucesor in sucesores do     ;iteramos por cada sucesor generado
                    (let* ((resultado-sucesor (minimax-1-a-b-sort sucesor (1+ profundidad)  ;Calculamos su valor minimax
                                        nil profundidad-max (- beta) (- alfa) f-eval))
                           (valor-nuevo (- resultado-sucesor)))   ;Y lo negamos (negamax)
                      (when (> valor-nuevo alfa)  ;Actualizamos el alfa al ser nodo max (todos lo son)
                        (setq alfa valor-nuevo)
                        (setq mejor-sucesor  sucesor))
                      (when ( <= beta alfa)        ;si alfa es mayor que beta, podamos (no exploramos las demas ramas de ese nodo)
                        (return (if  devolver-movimiento mejor-sucesor alfa)))))
                  (if  devolver-movimiento mejor-sucesor alfa)))))))



;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;
;;; minimax-a-b-unsort
;;;
;;; minimax con poda pero que ademas ordena de menor a mayor valor heuristico los sucesores.
;;;

(defun minimax-a-b-unsort (estado profundidad-max f-eval)
    (let* ((oldverb *verb*) ; Si la bandera de debug esta puesta modo verboso en la ejecucion de minimax. Si no modo vervoso desactivado
         (*verb* (if *debug-mmx* *verb* nil))
         (estado2 (minimax-1-a-b-unsort estado 0 t profundidad-max -99999 99999 f-eval)) ; Llamada a minimax desde la profundidad inicial 0 (modo verboso o no). Es decir empezamos la busqueda como tal.
         (*verb* oldverb)) ;Restabecemos el valor de *verb*.
    estado2))



 (defun minimax-1-a-b-unsort (estado profundidad devolver-movimiento profundidad-max alfa beta f-eval)
  (cond ((>= profundidad profundidad-max) ;En caso de llegar a la profundidad maxima estabelcida. Paramos la busqueda y evaluamos el tablero.
         (unless devolver-movimiento  (funcall f-eval estado))) ;Si la bandera devolver-movimiento esta a nil devolvemos el valor del estado del tablero segun nuestra heuristica.
         (t
         (let* ((sucesores (sort (generar-sucesores estado) #'> :key f-eval))
                (mejor-sucesor nil))
           (cond ((null sucesores) ;Si ya no quedan sucecesores en la lista hemos llegado al final de nuestra exploracion y evaluamos el estado.
                  (unless devolver-movimiento  (funcall f-eval estado)))
                  (t
                  (loop for sucesor in sucesores do     ;iteramos por cada sucesor generado
                    (let* ((resultado-sucesor (minimax-1-a-b-unsort sucesor (1+ profundidad)  ;Calculamos su valor minimax
                                        nil profundidad-max (- beta) (- alfa) f-eval))
                           (valor-nuevo (- resultado-sucesor)))   ;Y lo negamos (negamax)
                      (when (> valor-nuevo alfa)  ;Actualizamos el alfa al ser nodo max (todos lo son)
                        (setq alfa valor-nuevo)
                        (setq mejor-sucesor  sucesor))
                      (when ( <= beta alfa)        ;si alfa es mayor que beta, podamos (no exploramos las demas ramas de ese nodo)
                        (return (if  devolver-movimiento mejor-sucesor alfa)))))
                  (if  devolver-movimiento mejor-sucesor alfa)))))))


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;; Jugador analizado
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

(defun aux1-snape (estado)
  (- (get-fichas (estado-tablero estado) (estado-lado-sgte-jugador estado) 6)
      (get-fichas (estado-tablero estado) (lado-contrario (estado-lado-sgte-jugador estado)) 6)))

(defun aux2-snape (estado)
  (- (get-fichas (estado-tablero estado) (estado-lado-sgte-jugador estado) 5)
      (get-fichas (estado-tablero estado) (lado-contrario (estado-lado-sgte-jugador estado)) 5)))


(defun aux3-snape (estado)
  (- (get-fichas (estado-tablero estado) (estado-lado-sgte-jugador estado) 0)
      (get-fichas (estado-tablero estado) (lado-contrario (estado-lado-sgte-jugador estado)) 0)))

(defun f-eval-snape (estado)
  (+ (* 5 (aux1-snape estado)) (* 2 (aux2-snape estado)) (aux3-snape estado)))


(setf *jdr-mmx-snape* (make-jugador
                        :nombre   '|Ju-Mmx-snape|
                        :f-juego  #'f-j-mmx
                        :f-eval   #'f-eval-snape))


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;; Funcion auxiliar para compara tiempos entre versiones de minimax
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

;;; Jugadores auxiliares

;;; Pareja de jugadores con la misma heuristica y que minimax sin poda.
(setf *mi-jugador-pruebas-1* (make-jugador
    :nombre 'Jugador-Minimax-1
    :f-juego #'f-j-mmx
    :f-eval #'f-eval-snape))

(setf *mi-jugador-pruebas-2* (make-jugador
    :nombre 'Jugador-Minimax-2
    :f-juego #'f-j-mmx
    :f-eval #'f-eval-snape))

;;; Pareja de jugadores con la misma heuristica y que minimax con poda
(setf *mi-jugador-pruebas-a-b-1* (make-jugador
    :nombre 'Jugador-Minimax-1-a-b
    :f-juego #'f-j-mmx-a-b
    :f-eval #'f-eval-snape))

(setf *mi-jugador-pruebas-a-b-2* (make-jugador
    :nombre 'Jugador-Minimax-2-a-b
    :f-juego #'f-j-mmx-a-b
    :f-eval #'f-eval-snape))

;;; Pareja de jugadores con la misma heuristica y que minimax con poda y ordenacion de nodos.
(setf *mi-jugador-pruebas-a-b-1-sort* (make-jugador
    :nombre 'Jugador-Minimax-1-sort
    :f-juego #'f-j-mmx-a-b-sort
    :f-eval #'f-eval-snape))

(setf *mi-jugador-pruebas-a-b-2-sort* (make-jugador
    :nombre 'Jugador-Minimax-2-sort
    :f-juego #'f-j-mmx-a-b-sort
    :f-eval #'f-eval-snape))


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;
;;; estadistico
;;; funcion que imprime en un fichero "nombre" el tiempo en ciclos de reloj de las partidas con profundidad 1 hasta max-prof entre
;;; (i) jugadores normales sin poda
;;; (ii) jugadores con poda
;;; (iii) jugadores con poda y ordenacion
;;;
;;; Todos los jugadores usan la misma heuristica y estan definidos antes
;;;
;;; Cuestiones de diseno:
;;;  Se podria haber creado a los jugadores dentro del cuerpo de la funcion pero como en el fondo todo este fichero es de prueba y conclusiones
;;;  sobre el algoritmo minimax pensamos que no es execivamente importante crear una funcion autonoma sino las conclusiones que se pueden sacar
;;;  de los resultados.
;;;
;;; Foramto de impresion:
;;;  profundidad i : tiempo-partida-sin-poda tiempo-partida-con-poda tiempo-partida-con-poda-y-ordenacion
;;;
;;; Objetivo ver como evoluciona el algoritmo con poda y la ordenacion de los sucesores con la profundidad
;;;

(defun estadistico( fichero max-prof)

  (setf path (make-pathname :name fichero))
  (setf str (open path :direction :output
                        :if-exists :supersede))
  (setf start 1)
  (do ((i start (1+ i )))
    ((> i max-prof))
      ;; Tiempo sin poda
      (setf before (get-internal-run-time))
      (partida 0 i (list *mi-jugador-pruebas-1* *mi-jugador-pruebas-2*))
      (setf time1 (- (get-internal-run-time)  before))
      ;; Tiempo con poda sin ordenacion
      (setf before (get-internal-run-time))
      (partida 0 i (list *mi-jugador-pruebas-a-b-1* *mi-jugador-pruebas-a-b-2*))
      (setf time2 (- (get-internal-run-time)  before))
      ;; Tiempo con poda y ordenacion
      (setf before (get-internal-run-time))
      (partida 0 i (list *mi-jugador-pruebas-a-b-1-sort* *mi-jugador-pruebas-a-b-2-sort*))
      (setf time3 (- (get-internal-run-time)  before))
      (format str "Profundidad ~D: ~D ~D ~D ~%" i time1 time2 time3))
      (close str))

;; Profundidades muy grandes pueden generar tiempos de espera muy grandes