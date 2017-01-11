%{
#include <stdio.h>
#include "alfa.h"
#include "generacion.h"
#include "tablaHash.h"
#include "tablaSimbolos.h"
#include <string.h>

#define LOCAL 1
#define GLOBAL 0

extern FILE* outF;
extern int yycolumn;
extern int yycolumnO;
extern int yylineno;
extern int yylex(void);
extern int tipoError;
int yyerror(char *s);
TABLA_HASH *tablaGlobal = NULL, *tablaLocal = NULL;
int tipo_actual;
int clase_actual;
int tamanio_vector_actual;
int pos_variable_local_actual = 1;
int pos_parametro_actual;
int num_variables_locales_actual;
int num_parametros_actual = 1;
int num_parametros_llamada_actual = 0;
int tipo_funcion_actual;
int fn_return = 0;
int ambito;
char spf[100];
INFO_SIMBOLO * busq;
int ncomp = 0;
int etiqueta = 0;
int en_explist = 0;
%}

%union {
	tipo_atributos atributos;
}

%token TOK_MAIN
%token TOK_INT
%token TOK_BOOLEAN 
%token TOK_ARRAY
%token TOK_FUNCTION
%token TOK_IF
%token TOK_ELSE
%token TOK_WHILE 
%token TOK_SCANF
%token TOK_PRINTF 
%token TOK_RETURN

%token TOK_PUNTOYCOMA
%token TOK_COMA 
%token TOK_PARENTESISIZQUIERDO
%token TOK_PARENTESISDERECHO 
%token TOK_CORCHETEIZQUIERDO
%token TOK_CORCHETEDERECHO
%token TOK_LLAVEIZQUIERDA
%token TOK_LLAVEDERECHA
%token TOK_ASIGNACION
%token TOK_MAS
%token TOK_MENOS
%token TOK_DIVISION
%token TOK_ASTERISCO
%token TOK_AND
%token TOK_OR
%token TOK_NOT
%token TOK_IGUAL
%token TOK_DISTINTO
%token TOK_MENORIGUAL
%token TOK_MAYORIGUAL
%token TOK_MENOR
%token TOK_MAYOR

%token <atributos> TOK_IDENTIFICADOR

%token <atributos> TOK_CONSTANTE_ENTERA
%token <atributos> TOK_TRUE
%token <atributos> TOK_FALSE

%token TOK_ERROR

%type <atributos> exp
%type <atributos> elemento_vector
%type <atributos> comparacion
%type <atributos> constante
%type <atributos> if_exp
%type <atributos> while
%type <atributos> while_exp
%type <atributos> if_exp_sentencias
%type <atributos> fn_name
%type <atributos> idf_llamada_funcion
%type <atributos> fn_declaration

%left TOK_MAS TOK_MENOS TOK_OR
%left TOK_ASTERISCO TOK_DIVISION TOK_AND
%right MENOSU TOK_NOT

%%

programa: inicio TOK_MAIN TOK_LLAVEIZQUIERDA declaraciones escritura_ts funciones escritura_main sentencias TOK_LLAVEDERECHA fin { yycolumnO = yycolumn; fprintf( outF, ";R1:	<programa> ::= main { <declaraciones> <funciones> <sentencias> }\n" ); };

inicio: { tablaGlobal = crear_tabla(1000);
	escribir_cabecera_compatibilidad( outF );
	escribir_subseccion_data( outF );
	escribir_cabecera_bss( outF );
	ambito = GLOBAL;
};

escritura_ts: {
	escribir_segmento_codigo(outF);
};

escritura_main: { 
	escribir_inicio_main(outF);
};

fin: { liberar_tabla( tablaGlobal );
	escribir_fin( outF );
};

declaraciones: declaracion { yycolumnO = yycolumn; fprintf( outF, ";R2:	<declaraciones> ::= <declaracion>\n" ); }
	| declaracion declaraciones { yycolumnO = yycolumn; fprintf( outF, ";R3:	<declaraciones> ::= <declaracion> <declaraciones>\n" ); };

declaracion: clase identificadores TOK_PUNTOYCOMA { yycolumnO = yycolumn; fprintf( outF, ";R4:	<declaracion> ::= <clase> <identificadores> ;\n" ); };

clase: clase_escalar { yycolumnO = yycolumn; fprintf( outF, ";R5:	<clase> ::= <clase_escalar>\n" ); clase_actual = ESCALAR; tamanio_vector_actual = 1; }
	| clase_vector { yycolumnO = yycolumn; fprintf( outF, ";R7:	<clase> ::= <clase_vector>\n" ); clase_actual = VECTOR; };

clase_escalar: tipo { yycolumnO = yycolumn; fprintf( outF, ";R9:	<clase_escalar> ::= <tipo>\n" ); };

tipo: TOK_INT { yycolumnO = yycolumn; fprintf( outF, ";R10:	<tipo> ::= int\n" ); tipo_actual = ENTERO; }
	| TOK_BOOLEAN { yycolumnO = yycolumn; fprintf( outF, ";R11:	<tipo> ::= boolean\n" ); tipo_actual = BOOLEANO; };

clase_vector: TOK_ARRAY tipo TOK_CORCHETEIZQUIERDO TOK_CONSTANTE_ENTERA TOK_CORCHETEDERECHO { yycolumnO = yycolumn; fprintf( outF, ";R15:	<clase_vector> ::= array <tipo> [ <constante_entera> ]\n" );
	tamanio_vector_actual = $4.valor_entero;
	if( tamanio_vector_actual < 1 || tamanio_vector_actual > MAX_TAMANIO_VECTOR ) {
		fprintf(stderr, "Error semantico: longitud de vector invalida: %d\n",tamanio_vector_actual );
		return 1;
	}
	
};

identificadores: identificador { yycolumnO = yycolumn; fprintf( outF, ";R18:	<identificadores> ::= <identificador>\n" ); }
	| identificador TOK_COMA identificadores { yycolumnO = yycolumn; fprintf( outF, ";R19:	<identificadores> ::= <identificador> , <identificadores>\n" ); };

funciones: funcion funciones { yycolumnO = yycolumn; fprintf( outF, ";R20:	<funciones> ::= <funcion> <funciones>\n" ); }
	| { yycolumnO = yycolumn; fprintf( outF, ";R21:	<funciones> ::= \n" ); };

funcion: fn_declaration sentencias TOK_LLAVEDERECHA { yycolumnO = yycolumn; fprintf( outF, ";R22:	<funcion> ::= function <tipo> <identificador> ( <parametros_funcion> ) { <declaraciones_funcion> <sentencias> }\n" );
	fprintf( outF, "\tmov esp, ebp\n" );
	fprintf( outF, "\tpop ebp\n" );
	fprintf( outF, "\tret\n" );
	liberar_tabla(tablaLocal);
	tablaLocal = NULL;
	ambito = GLOBAL;
	if( fn_return == 0 ){
		fprintf(stderr, "Error semantico: funcion %s sin return en [lin %d, col %d]\n", $1.lexema, yylineno, yycolumnO );
		return 1;
	}
	/*Actualizar numero de parametros*/
};

fn_name: TOK_FUNCTION tipo TOK_IDENTIFICADOR {
	if( ambito == GLOBAL ) {
		if( declararFuncion( tablaGlobal, &tablaLocal, $3.lexema, tipo_actual, clase_actual, 0, 0 ) == OK ) {
			ambito = LOCAL;
			num_variables_locales_actual = 0;
			pos_variable_local_actual = 1;
			num_parametros_actual = 0;
			pos_parametro_actual = 0;
			tipo_funcion_actual = tipo_actual;
			fn_return = 0;
			strcpy($$.lexema, $3.lexema);
		}
		else {
			fprintf(stderr, "Error semantico: identificador %s duplicado en [lin %d, col %d]\n", $3.lexema, yylineno, yycolumnO );
			return 1;
		}
	}
	else {
		fprintf(stderr, "Error semantico: funcion anidada en [lin %d, col %d]\n", yylineno, yycolumnO );
		return 1;
	}
};

fn_declaration: fn_name TOK_PARENTESISIZQUIERDO parametros_funcion TOK_PARENTESISDERECHO TOK_LLAVEIZQUIERDA declaraciones_funcion {
	busq = usoGlobal( tablaGlobal, $1.lexema );
	busq->adicional1 = num_parametros_actual;
	busq->adicional2 = num_variables_locales_actual;
	busq = usoLocal( tablaGlobal, tablaLocal, $1.lexema );
	busq->adicional1 = num_parametros_actual;
	busq->adicional2 = num_variables_locales_actual;
	strcpy($$.lexema, $1.lexema);
	fprintf( outF, "_%s:\n", $1.lexema );
	fprintf( outF, "\tpush ebp\n" );
	fprintf( outF, "\tmov ebp, esp\n" );
	fprintf( outF, "\tsub esp, 4*%d\n", num_variables_locales_actual );
};

parametros_funcion: parametro_funcion resto_parametros_funcion { yycolumnO = yycolumn; fprintf( outF, ";R23:	<parametros_funcion> ::= <parametro_funcion> <resto_parametros_funcion>\n" ); }
	| { yycolumnO = yycolumn; fprintf( outF, ";R24:	<parametros_funcion> ::=\n" ); };

resto_parametros_funcion: TOK_PUNTOYCOMA parametro_funcion resto_parametros_funcion { yycolumnO = yycolumn; fprintf( outF, ";R25:	<resto_parametros_funcion> ::= ; <parametro_funcion> <resto_parametros_funcion>\n" ); }
	| { yycolumnO = yycolumn; fprintf( outF, ";R26:	<resto_parametros_funcion> ::=\n" ); };

parametro_funcion: tipo idpf { yycolumnO = yycolumn; fprintf( outF, ";R27:	<parametro_funcion> ::= <tipo> <identificador>\n" ); };

idpf: TOK_IDENTIFICADOR {
	if( declararLocal( tablaLocal, $1.lexema, PARAMETRO, tipo_actual, clase_actual, tamanio_vector_actual, pos_parametro_actual ) == OK ) {
		pos_parametro_actual++;
		num_parametros_actual++;
	}
	else {
		fprintf(stderr, "Error semantico: identificador %s duplicado en [lin %d, col %d]\n", $1.lexema, yylineno, yycolumnO );
		return 1;
	}
};

declaraciones_funcion: declaraciones { yycolumnO = yycolumn; fprintf( outF, ";R28:	<declaraciones_funcion> ::= <declaraciones>\n" ); }
	| { yycolumnO = yycolumn; fprintf( outF, ";R29:	<declaraciones_funcion> ::=\n" ); };

sentencias:	sentencia { yycolumnO = yycolumn; fprintf( outF, ";R30:	<sentencias> ::= <sentencia>\n" ); }
	| sentencia sentencias { yycolumnO = yycolumn; fprintf( outF, ";R31:	<sentencias> ::= <sentencia> <sentencias>\n" ); };

sentencia: sentencia_simple TOK_PUNTOYCOMA { yycolumnO = yycolumn; fprintf( outF, ";R32:	<sentencia> ::= <sentencia_simple> ;\n" ); }
	| bloque { yycolumnO = yycolumn; fprintf( outF, ";R33:	<sentencia> ::= <bloque>\n" ); };

sentencia_simple: asignacion { yycolumnO = yycolumn; fprintf( outF, ";R34:	<sentencia_simple> ::= <asignacion>\n" ); }
	| lectura { yycolumnO = yycolumn; fprintf( outF, ";R35:	<sentencia_simple> ::= <lectura>\n" ); }
	| escritura { yycolumnO = yycolumn; fprintf( outF, ";R36:	<sentencia_simple> ::= <escritura>\n" ); }
	| retorno_funcion { yycolumnO = yycolumn; fprintf( outF, ";R38:	<sentencia_simple> ::= <retorno_funcion>\n" ); };

bloque: condicional { yycolumnO = yycolumn; fprintf( outF, ";R40:	<bloque> ::= <condicional>\n" ); }
	|bucle { yycolumnO = yycolumn; fprintf( outF, ";R41:	<bloque> ::= <bucle>\n" ); };

asignacion: TOK_IDENTIFICADOR TOK_ASIGNACION exp { yycolumnO = yycolumn; fprintf( outF, ";R43:	<asignacion> ::= <identificador> = <exp>\n" );
		if( ambito == LOCAL ) {
			busq = buscar_simbolo( tablaLocal, $1.lexema );
			if( busq == NULL )busq = buscar_simbolo( tablaGlobal, $1.lexema );
		}
		else busq = buscar_simbolo( tablaGlobal, $1.lexema );
		if( busq != NULL ) {
			if( ambito == GLOBAL ) {
				if( busq->tipo == $3.tipo ) {
					if( busq->clase == ESCALAR ) asignar( outF, $1.lexema, !($3.es_direccion) );
					else {
						fprintf(stderr, "Error semantico: vector no indexado [lin %d, col %d]\n", yylineno, yycolumnO );
						return 1;
					}
				}
				else {
					fprintf(stderr, "Error semantico: asignacion de tipos distintos en [lin %d, col %d]\n", yylineno, yycolumnO );
					return 1;
				}
			}
			else {
				if( busq->tipo == $3.tipo ) {
					if( busq->categoria == VARIABLE ) {
						fprintf(outF, "\tpop dword ebx\n");
						if( $3.es_direccion ) fprintf(outF, "\tmov dword ebx, [ebx]\n");
						fprintf( outF, "\tlea eax, [ebp-4*%d]\n", busq->adicional2 );
						fprintf(outF, "\tmov dword [eax], ebx\n");
						
					}
					else if( busq->categoria == PARAMETRO ) {
						fprintf(outF, "\tpop dword ebx\n");
						if( $3.es_direccion ) fprintf(outF, "\tmov dword ebx, [ebx]\n");
						fprintf( outF, "\tlea eax, [ebp+4+4*(%d-%d)]\n", num_parametros_actual, busq->adicional2 );
						fprintf(outF, "\tmov dword [eax], ebx\n");
					}
				}
				else {
					fprintf(stderr, "Error semantico: asignacion de tipos distintos en [lin %d, col %d]\n", yylineno, yycolumnO );
					return 1;
				}
			}
		}
		else {
			fprintf(stderr, "Error semantico: identificador %s no declarado en [lin %d, col %d]\n", $1.lexema, yylineno, yycolumnO );
			return 1;
		}
	}
	| elemento_vector TOK_ASIGNACION exp	{ yycolumnO = yycolumn; fprintf( outF, ";R44:	<asignacion> ::= <elemento_vector> = <exp>\n" );
		if ( $1.tipo == $3.tipo ) {
			asignarVector( outF, !($3.es_direccion) );
		}
		else {
			fprintf(stderr, "Error semantico: asignacion de tipos distintos en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
	};

elemento_vector: TOK_IDENTIFICADOR TOK_CORCHETEIZQUIERDO exp TOK_CORCHETEDERECHO { yycolumnO = yycolumn; fprintf( outF, ";R48:	<elemento_vector> ::= <identificador> [ <exp> ]\n" );
		if( ambito == LOCAL ) {
			busq = buscar_simbolo( tablaLocal, $1.lexema );
			if( busq == NULL )busq = buscar_simbolo( tablaGlobal, $1.lexema );
		}
		else busq = buscar_simbolo( tablaGlobal, $1.lexema );
		if( busq != NULL ) {
			if( busq->clase == VECTOR ) {
				if( $3.tipo == ENTERO ) {
					$$.tipo = busq->tipo;
					$$.es_direccion = 1;
					fprintf(outF, "\tpop dword eax\n");
					if($3.es_direccion == 1) fprintf(outF, "\tmov dword eax , [eax]\n");
					fprintf(outF, "\tcmp eax, 0\n");
					fprintf(outF, "\tjl near error2\n");
					fprintf(outF, "\tcmp eax, %d\n", busq->adicional1);
					fprintf(outF, "\tjge near error2\n");
					fprintf(outF, "\tmov dword edx, _%s\n", $1.lexema);
					fprintf(outF, "\tlea eax, [edx + eax*4]\n" );
					fprintf(outF, "\tpush dword eax\n" );
				}
				else {
					fprintf(stderr, "Error semantico: indexacion con un valor no entero en [lin %d, col %d]\n", yylineno, yycolumnO );
					return 1;
				}
			}
			else {
				fprintf(stderr, "Error semantico: uso de un escalar como vector en [lin %d, col %d]\n", yylineno, yycolumnO );
				return 1;
			}
		}
		else {
			fprintf(stderr, "Error semantico: identificador %s no declarado en [lin %d, col %d]\n", $1.lexema, yylineno, yycolumnO );
			return 1;
		}
};

condicional: if_exp sentencias TOK_LLAVEDERECHA { yycolumnO = yycolumn; fprintf( outF, ";R50:	<condicional> ::= if ( <exp> ) { <sentencias> }\n" );
	fprintf(outF, "\tfin_si%d:\n", $1.etiqueta);
}
	|if_exp_sentencias TOK_ELSE TOK_LLAVEIZQUIERDA sentencias TOK_LLAVEDERECHA { yycolumnO = yycolumn; fprintf( outF, ";R51:	<condicional> ::= if ( <exp> ) { <sentencias> } else { <sentencias> }\n" );
	fprintf(outF, "\tfin_sino%d:\n", $1.etiqueta);
};

if_exp: TOK_IF TOK_PARENTESISIZQUIERDO exp TOK_PARENTESISDERECHO TOK_LLAVEIZQUIERDA {
	if( $3.tipo == BOOLEANO ) {
		$$.etiqueta = etiqueta++;
		fprintf(outF, "\tpop dword eax\n");
		if( $3.es_direccion ) fprintf(outF, "\tmov eax, [eax]\n");
		fprintf(outF, "\tcmp eax, 0\n");
		fprintf(outF, "\tje fin_si%d\n", $$.etiqueta);
	}
	else {
		fprintf(stderr, "Error semantico: if con condicion no booleana en [lin %d, col %d]\n", yylineno, yycolumnO );
		return 1;
	}
};

if_exp_sentencias: if_exp sentencias TOK_LLAVEDERECHA {
	$$.etiqueta = $1.etiqueta;
	fprintf(outF, "\tjmp near fin_sino%d\n", $1.etiqueta);
	fprintf(outF, "\tfin_si%d:\n", $1.etiqueta);
};

bucle: while_exp TOK_PARENTESISDERECHO TOK_LLAVEIZQUIERDA sentencias TOK_LLAVEDERECHA { yycolumnO = yycolumn; fprintf( outF, ";R52:	<bucle> ::= while ( <exp> ) { <sentencias> }\n" );
	fprintf(outF, "\tjmp near inicio_while%d\n", $1.etiqueta);
	fprintf(outF, "\tfin_while%d:\n", $1.etiqueta);
};

while: TOK_WHILE TOK_PARENTESISIZQUIERDO {
	$$.etiqueta = etiqueta++;
	fprintf(outF, "\tinicio_while%d:\n", $$.etiqueta);
};

while_exp: while exp {
	if( $2.tipo == BOOLEANO ) {
		$$.etiqueta = $1.etiqueta;
		fprintf(outF, "\tpop dword eax\n");
		if( $2.es_direccion ) fprintf(outF, "\tmov eax, [eax]\n");
		fprintf(outF, "\tcmp eax, 0\n");
		fprintf(outF, "\tje fin_while%d\n", $1.etiqueta);
	}
	else {
		fprintf(stderr, "Error semantico: while con condicion no booleana en [lin %d, col %d]\n", yylineno, yycolumnO );
		return 1;
	}
};

lectura: TOK_SCANF TOK_IDENTIFICADOR { yycolumnO = yycolumn; fprintf( outF, ";R54:	<lectura> ::= scanf <identificador>\n" );
	if( ambito == LOCAL ) {
		busq = buscar_simbolo( tablaLocal, $2.lexema );
		if( busq == NULL )busq = buscar_simbolo( tablaGlobal, $2.lexema );
	}
	else busq = buscar_simbolo( tablaGlobal, $2.lexema );
	if( busq != NULL ) {
		if( ambito == GLOBAL ) leer( outF, $2.lexema, busq->tipo );
		else {
			if( busq->categoria == PARAMETRO ) {
				fprintf( outF, "\tlea eax, [ebp+4+4*(%d-%d)]\n", num_parametros_actual, busq->adicional2 );
				fprintf( outF, "\tpush dword eax\n" );
				fprintf( outF, "\tcall scan_%s\n", busq->tipo==ENTERO?"int":"boolean");
				fprintf( outF, "\tadd esp, 4\n");
			}
			else if( busq->categoria == VARIABLE ) {
				fprintf( outF, "\tlea eax, [ebp-4*%d]\n", busq->adicional2 );
				fprintf( outF, "\tpush dword eax\n" );
				fprintf( outF, "\tcall scan_%s\n", busq->tipo==ENTERO?"int":"boolean");
				fprintf( outF, "\tadd esp, 4\n");
			}
		}
	}
	else {
		fprintf(stderr, "Error semantico: identificador %s no declarado en [lin %d, col %d]\n", $2.lexema, yylineno, yycolumnO );
		return 1;
	}
};

escritura: TOK_PRINTF exp { yycolumnO = yycolumn; fprintf( outF, ";R56:	<escritura> ::= printf <exp>\n" );
	escribir( outF, !($2.es_direccion), $2.tipo );
};

retorno_funcion: TOK_RETURN  exp { yycolumnO = yycolumn; fprintf( outF, ";R61:	<retorno_funcion> ::= return <exp>\n" );
	if( ambito == LOCAL ) {
		if( tipo_funcion_actual == $2.tipo ) {
			fn_return = 1;
			fprintf( outF, "\tpop dword eax\n" );
			if( $2.es_direccion )fprintf( outF, "\tmov dword eax, [eax]\n" );
			fprintf( outF, "\tmov dword esp, ebp\n" );
			fprintf( outF, "\tpop dword ebp\n" );
			fprintf( outF, "\tret\n" );
			
		}
		else {
			fprintf(stderr, "Error semantico: return de un tipo distinto al de la funcion en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
	}
	else {
		fprintf(stderr, "Error semantico: return fuera del cuerpo de una funcion en [lin %d, col %d]\n", yylineno, yycolumnO );
		return 1;
	}
};

exp: exp TOK_MAS exp { yycolumnO = yycolumn; fprintf( outF, ";R72:	<exp> ::= <exp> + <exp>\n" );
		if( $1.tipo != ENTERO || $3.tipo != ENTERO ) {
			fprintf(stderr, "Error semantico: suma con elementos no enteros en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = ENTERO;
		$$.es_direccion = 0;

		sumar( outF, !($1.es_direccion), !($3.es_direccion) );
	}
	| exp TOK_MENOS exp { yycolumnO = yycolumn; fprintf( outF, ";R73:	<exp> ::= <exp> - <exp>\n" );
		if( $1.tipo != ENTERO || $3.tipo != ENTERO ) {
			fprintf(stderr, "Error semantico: resta con elementos no enteros en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = ENTERO;
		$$.es_direccion = 0;

		restar( outF, !($1.es_direccion), !($3.es_direccion) );
	}
	| exp TOK_DIVISION exp { yycolumnO = yycolumn; fprintf( outF, ";R74:	<exp> ::= <exp> / <exp>\n" );
		if( $1.tipo != ENTERO || $3.tipo != ENTERO ) {
			fprintf(stderr, "Error semantico: division con elementos no enteros en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = ENTERO;
		$$.es_direccion = 0;

		dividir( outF, !($1.es_direccion), !($3.es_direccion) );
	}
	| exp TOK_ASTERISCO exp { yycolumnO = yycolumn; fprintf( outF, ";R75:	<exp> ::= <exp> * <exp>\n" );
		if( $1.tipo != ENTERO || $3.tipo != ENTERO ) {
			fprintf(stderr, "Error semantico: multiplicacion con elementos no enteros en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = ENTERO;
		$$.es_direccion = 0;

		multiplicar( outF, !($1.es_direccion), !($3.es_direccion) );
	}
	| TOK_MENOS exp %prec MENOSU { yycolumnO = yycolumn; fprintf( outF, ";R76:	<exp> ::= - <exp>\n" );
		if( $2.tipo != ENTERO ) {
			fprintf(stderr, "Error semantico: cambio de signo de un elemento no entero en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = ENTERO;
		$$.es_direccion = 0;

		cambiar_signo( outF, !($2.es_direccion) );
	}
	| exp TOK_AND exp { yycolumnO = yycolumn; fprintf( outF, ";R77:	<exp> ::= <exp> && <exp>\n" );
		if( $1.tipo != BOOLEANO || $3.tipo != BOOLEANO ) {
			fprintf(stderr, "Error semantico: and con elementos no booleanos en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = BOOLEANO;
		$$.es_direccion = 0;

		y( outF, !($1.es_direccion), !($3.es_direccion) );
	}
	| exp TOK_OR exp { yycolumnO = yycolumn; fprintf( outF, ";R78:	<exp> ::= <exp> || <exp>\n" );
		if( $1.tipo != BOOLEANO || $3.tipo != BOOLEANO ) {
			fprintf(stderr, "Error semantico: or con elementos no booleanos en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = BOOLEANO;
		$$.es_direccion = 0;

		o( outF, !($1.es_direccion), !($3.es_direccion) );
	}
	| TOK_NOT exp { yycolumnO = yycolumn; fprintf( outF, ";R79:	<exp> ::= ! <exp>\n" );
		if( $2.tipo != BOOLEANO ) {
			fprintf(stderr, "Error semantico: not de un elemento no booleano en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = BOOLEANO;
		$$.es_direccion = 0;

		no( outF, !($2.es_direccion), ncomp );
		ncomp++;
	}
	| TOK_IDENTIFICADOR { yycolumnO = yycolumn; fprintf( outF, ";R80:	<exp> ::= <identificador>\n" );
		if( ambito == LOCAL ) {
			busq = buscar_simbolo( tablaLocal, $1.lexema );
			if( busq == NULL )busq = buscar_simbolo( tablaGlobal, $1.lexema );
		}
		else busq = buscar_simbolo( tablaGlobal, $1.lexema );
		if( busq != NULL ) {
			if( ambito == GLOBAL ) {
				if( busq->clase == ESCALAR ) {
					escribir_operando( outF, $1.lexema, 1 );
					if( en_explist == 1 ) {
						fprintf( outF, "\tpop dword eax\n" );
						fprintf( outF, "\tpush dword [eax]\n" );
						$$.es_direccion = 0;
					}
					else {
						$$.es_direccion = 1;
					}
					$$.tipo = busq->tipo;
				}
				else {
					fprintf(stderr, "Error semantico: vector no indexado en [lin %d, col %d]\n", yylineno, yycolumnO );
					return 1;
				}
			}
			else {
				if( busq->categoria == PARAMETRO ) {
					fprintf( outF, "\tlea eax, [ebp+4+4*(%d-%d)]\n", num_parametros_actual, busq->adicional2 );
					fprintf( outF, "\tpush dword eax\n" );
					$$.tipo = busq->tipo;
					$$.es_direccion = 1;
				}
				else if( busq->categoria == VARIABLE ) {
					fprintf( outF, "\tlea eax, [ebp-4*%d]\n", busq->adicional2 );
					fprintf( outF, "\tpush dword eax\n" );
					$$.tipo = busq->tipo;
					$$.es_direccion = 1;
				}
			}
		}
		else {
			fprintf(stderr, "Error semantico: identificador %s no declarado en [lin %d, col %d]\n", $1.lexema, yylineno, yycolumnO );
			return 1;
		}
	}
	| constante { yycolumnO = yycolumn; fprintf( outF, ";R81:	<exp> ::= <constante>\n" );
		$$.tipo = $1.tipo;
		$$.es_direccion = 0;
	}
	| TOK_PARENTESISIZQUIERDO exp TOK_PARENTESISDERECHO { yycolumnO = yycolumn; fprintf( outF, ";R82:	<exp> ::= ( <exp> )\n" );
		$$.tipo = $2.tipo;
		$$.es_direccion = $2.es_direccion;
	}
	| TOK_PARENTESISIZQUIERDO comparacion TOK_PARENTESISDERECHO { yycolumnO = yycolumn; fprintf( outF, ";R83:	<exp> ::= ( <comparacion> )\n" );
		$$.tipo = BOOLEANO;
		$$.es_direccion = 0;
	}
	| elemento_vector { yycolumnO = yycolumn; fprintf( outF, ";R85:	<exp> ::= <elemento_vector>\n" );
		$$.tipo = busq->tipo;
		$$.es_direccion = 1;
		/*No hace falta imprimir el operando porque ya se ha hecho en la regla 48*/
	}
	| idf_llamada_funcion TOK_PARENTESISIZQUIERDO lista_expresiones TOK_PARENTESISDERECHO { yycolumnO = yycolumn; fprintf( outF, ";R88:	<exp> ::= <identificador> ( <lista_expresiones> )\n" );
		if( ambito == LOCAL ) {
			busq = buscar_simbolo( tablaLocal, $1.lexema );
			if( busq == NULL )busq = buscar_simbolo( tablaGlobal, $1.lexema );
		}
		else busq = buscar_simbolo( tablaGlobal, $1.lexema );
		if( busq != NULL ) {
			if( num_parametros_llamada_actual == busq->adicional1 ) {
				en_explist = 0;
				$$.tipo = busq->tipo;
				$$.es_direccion = 0;
				fprintf( outF, "\tcall _%s\n", $1.lexema );
				fprintf( outF, "\tadd esp, 4 * %d\n", num_parametros_llamada_actual );
				fprintf( outF, "\tpush dword eax\n" );
				
			}
			else {
				fprintf(stderr, "Error semantico: llamada a funcion con numero de parametros incorrecto en [lin %d, col %d]\n", yylineno, yycolumnO );
				return 1;
			}
		}
		else {
			fprintf(stderr, "Error semantico: identificador %s no declarado en [lin %d, col %d]\n", $1.lexema, yylineno, yycolumnO );
			return 1;
		}
	}

idf_llamada_funcion: TOK_IDENTIFICADOR {
	if( ambito == LOCAL ) {
		busq = buscar_simbolo( tablaLocal, $1.lexema );
		if( busq == NULL )busq = buscar_simbolo( tablaGlobal, $1.lexema );
	}
	else busq = buscar_simbolo( tablaGlobal, $1.lexema );
	if( busq != NULL ) {
		if( busq->categoria == FUNCION ) {
			if( en_explist == 0 ) {
				num_parametros_llamada_actual = 0;
				en_explist = 1;
				strcpy($$.lexema, $1.lexema);
			}
			else {
				fprintf(stderr, "Error semantico: llamada a funcion como argumento de funcion en [lin %d, col %d]\n", yylineno, yycolumnO );
				return 1;
			}
		}
		else {
			fprintf(stderr, "Error semantico: el identificador %s no es una funcion en [lin %d, col %d]\n", $1.lexema, yylineno, yycolumnO );
			return 1;
		}
	}
	else {
		fprintf(stderr, "Error semantico: identificador %s no declarado en [lin %d, col %d]\n", $1.lexema, yylineno, yycolumnO );
		return 1;
	}
}

lista_expresiones: exp resto_lista_expresiones { yycolumnO = yycolumn; fprintf( outF, ";R89:	<lista_expresiones> ::= <exp> <resto_lista_expresiones>\n" );
		num_parametros_llamada_actual++;
	}
	| { yycolumnO = yycolumn; fprintf( outF, ";R90:	<lista_expresiones> ::=\n" ); };

resto_lista_expresiones: TOK_COMA exp resto_lista_expresiones { yycolumnO = yycolumn; fprintf( outF, ";R91:	<resto_lista_expresiones> ::= , <exp> <resto_lista_expresiones>\n" );
		num_parametros_llamada_actual++;
		if( $2.es_direccion == 1 ) {
			fprintf( outF, "\tpop dword eax\n" );
			fprintf( outF, "\tpush dword [eax]\n" );
		}
	}
	| { yycolumnO = yycolumn; fprintf( outF, ";R92:	<resto_lista_expresiones> ::=\n" ); };

comparacion: exp TOK_IGUAL exp { yycolumnO = yycolumn; fprintf( outF, ";R93:	<comparacion> ::= <exp> == <exp>\n" );
		if( $1.tipo != ENTERO || $3.tipo != ENTERO ) {
			fprintf(stderr, "Error semantico: igual de elementos no enteros en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = BOOLEANO;
		$$.es_direccion = 0;
		/*a==b equivale a !(a-b)*/
		restar( outF, !($1.es_direccion), !($3.es_direccion) );
		no( outF, 1, ncomp );
		ncomp++;
	}
	| exp TOK_DISTINTO exp { yycolumnO = yycolumn; fprintf( outF, ";R94:	<comparacion> ::= <exp> != <exp>\n" );
		if( $1.tipo != ENTERO || $3.tipo != ENTERO ) {
			fprintf(stderr, "Error semantico: distinto de elementos no enteros en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = BOOLEANO;
		$$.es_direccion = 0;
		/*a!=b equivale a !!(a-b)*/
		restar( outF, !($1.es_direccion), !($3.es_direccion) );
		no( outF, 1, ncomp );
		ncomp++;
		no( outF, 1, ncomp );
		ncomp++;
	}
	| exp TOK_MENORIGUAL exp { yycolumnO = yycolumn; fprintf( outF, ";R95:	<comparacion> ::= <exp> <= <exp>\n" );
		if( $1.tipo != ENTERO || $3.tipo != ENTERO ) {
			fprintf(stderr, "Error semantico: menor o igual de elementos no enteros en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = BOOLEANO;
		$$.es_direccion = 0;
		menorigual( outF, !($1.es_direccion), !($3.es_direccion), ncomp );
		ncomp++;
	}
	| exp TOK_MAYORIGUAL exp { yycolumnO = yycolumn; fprintf( outF, ";R96:	<comparacion> ::= <exp> >= <exp>\n" );
		if( $1.tipo != ENTERO || $3.tipo != ENTERO ) {
			fprintf(stderr, "Error semantico: mayor o igual de elementos no enteros en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = BOOLEANO;
		$$.es_direccion = 0;
		mayorigual( outF, !($1.es_direccion), !($3.es_direccion), ncomp );
		ncomp++;
	}
	| exp TOK_MENOR exp { yycolumnO = yycolumn; fprintf( outF, ";R97:	<comparacion> ::= <exp> < <exp>\n" );
		if( $1.tipo != ENTERO || $3.tipo != ENTERO ) {
			fprintf(stderr, "Error semantico: menor de elementos no enteros en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = BOOLEANO;
		$$.es_direccion = 0;
		menor( outF, !($1.es_direccion), !($3.es_direccion), ncomp );
		ncomp++;
	}
	| exp TOK_MAYOR exp { yycolumnO = yycolumn; fprintf( outF, ";R98:	<comparacion> ::= <exp> > <exp>\n" );
		if( $1.tipo != ENTERO || $3.tipo != ENTERO ) {
			fprintf(stderr, "Error semantico: menor de elementos no enteros en [lin %d, col %d]\n", yylineno, yycolumnO );
			return 1;
		}
		$$.tipo = BOOLEANO;
		$$.es_direccion = 0;
		mayor( outF, !($1.es_direccion), !($3.es_direccion), ncomp );
		ncomp++;
	};

constante: constante_logica { yycolumnO = yycolumn; fprintf( outF, ";R99:	<constante> ::= <constante_logica>\n" );
		$$.tipo = BOOLEANO;
		$$.es_direccion = 0;
	}
	| constante_entera { yycolumnO = yycolumn; fprintf( outF, ";R100:	<constante> ::= <constante_entera>\n" );
		$$.tipo = ENTERO;
		$$.es_direccion = 0;
	};

constante_logica: TOK_TRUE { yycolumnO = yycolumn; fprintf( outF, ";R102:	<constante_logica> ::= true\n" );
		escribir_operando( outF, "1", 0 );
	}
	| TOK_FALSE { yycolumnO = yycolumn; fprintf( outF, ";R103:	<constante_logica> ::= false\n" );
		escribir_operando( outF, "0", 0 );
	};

constante_entera: TOK_CONSTANTE_ENTERA { yycolumnO = yycolumn; fprintf( outF, ";R104:	<constante_entera> ::= TOK_CONSTANTE_ENTERA\n" );
		sprintf( spf, "%d", $1.valor_entero );
		escribir_operando( outF, spf, 0 );
	}

identificador: TOK_IDENTIFICADOR { yycolumnO = yycolumn; fprintf( outF, ";R108:	<identificador> ::= TOK_IDENTIFICADOR\n" );
	if( ambito == GLOBAL ) {
		if( insertar_simbolo( tablaGlobal, $1.lexema, VARIABLE, tipo_actual, clase_actual, tamanio_vector_actual, 0 ) == OK ) {
			declarar_variable( outF, $1.lexema, tipo_actual, tamanio_vector_actual );
		}
		else {
			fprintf(stderr, "Error semantico: identificador %s duplicado en [lin %d, col %d]\n", $1.lexema, yylineno, yycolumnO );
			return 1;
		}
	}
	else {
		if( clase_actual == ESCALAR ) {
			if( insertar_simbolo( tablaLocal, $1.lexema, VARIABLE, tipo_actual, clase_actual, tamanio_vector_actual, pos_variable_local_actual ) == OK ) {
				pos_variable_local_actual++;
				num_variables_locales_actual++;
			}
			else {
				fprintf(stderr, "Error semantico: identificador %s duplicado en [lin %d, col %d]\n", $1.lexema, yylineno, yycolumnO );
				return 1;
			}
		}
		else {
			fprintf(stderr, "Error semantico: identificador %s de clase no escalar en ambito local en [lin %d, col %d]\n", $1.lexema, yylineno, yycolumnO );
			return 1;
		}
	}
};

%%

int yyerror(char* s) {
	if( !tipoError ) fprintf( stderr, "****Error sintactico en [lin %d, col %d]\n", yylineno, yycolumnO);
	if( tablaLocal != NULL ) liberar_tabla( tablaLocal );
	liberar_tabla( tablaGlobal );
	return 1;
}
