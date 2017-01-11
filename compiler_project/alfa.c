#include <stdio.h>
#include <stdlib.h>
#include "alfa.h"
#include "y.tab.h"

extern FILE* yyin;
FILE* outF;

int main(int argc, char** argv) {
	
	if( argc < 3 ) {
		printf( "Faltan argumentos\n" );
		exit( EXIT_FAILURE );
	}
	
	yyin = fopen( argv[1], "r" );
	if( yyin == NULL ) {
		perror( "yyin no abierto\n" );
		exit( EXIT_FAILURE );
	}
	outF = fopen( argv[2], "w" );
	if( outF == NULL ) {
		fclose( yyin );
		perror( "outF no abierto\n" );
		exit( EXIT_FAILURE );
	}

	yyparse();

	exit(EXIT_SUCCESS);
}
