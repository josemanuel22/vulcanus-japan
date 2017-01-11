#ifndef GENERACION_H
#define GENERACION_H

#include <stdio.h>

#define ENTERO      	0
#define BOOLEANO    	1
#define REAL        	2


void escribir_cabecera_compatibilidad(FILE* fpasm);

void escribir_subseccion_data(FILE* fpasm);

void escribir_cabecera_bss(FILE* fpasm);

void declarar_variable(FILE* fpasm, char * nombre,  int tipo,  int tamano);

void escribir_segmento_codigo(FILE* fpasm);

void escribir_inicio_main(FILE* fpasm);

void escribir_fin(FILE* fpasm);

void escribir_operando(FILE* fpasm, char * nombre, int es_var);

void asignarVector(FILE * fpasm, int es_inmediato);

void asignar(FILE * fpasm, char * nombre, int es_inmediato);

void sumar(FILE * fpasm, int es_inmediato_1, int es_inmediato_2);

void restar(FILE * fpasm, int es_inmediato_1, int es_inmediato_2);

void multiplicar(FILE * fpasm, int es_inmediato_1, int es_inmediato_2);

void dividir(FILE * fpasm, int es_inmediato_1, int es_inmediato_2);

void cambiar_signo(FILE * fpasm, int es_inmediato);

void o(FILE * fpasm, int es_inmediato_1, int es_inmediato_2);

void y(FILE * fpasm, int es_inmediato_1, int es_inmediato_2);

void no(FILE * fpasm, int es_inmediato, int cuantos_no);

void leer(FILE * fpasm, char * nombre, int tipo);

void escribir(FILE * fpasm, int es_inmediato, int tipo);

void menorigual( FILE * fpasm, int es_inmediato_1, int es_inmediato_2, int cuantos_mi );

void menor( FILE * fpasm, int es_inmediato_1, int es_inmediato_2, int cuantos_m );

void mayorigual( FILE * fpasm, int es_inmediato_1, int es_inmediato_2, int cuantos_mi );

void mayor( FILE * fpasm, int es_inmediato_1, int es_inmediato_2, int cuantos_m );

#endif
