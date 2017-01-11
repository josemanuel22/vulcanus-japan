
%{
 #include <stdio.h>
 #include "alfa.h"
 #include "y.tab.h"
 #include <string.h>

 int yycolumn = 1;
 int yycolumnO = 1;
 int tipoError = 0;
 extern FILE *outF;
%}

%option noyywrap
%option yylineno

%%

"//".*"\n" {yycolumnO = yycolumn = 1;}
[ ] {yycolumnO = ++yycolumn;}
[\t] {yycolumnO = yycolumn; yycolumn+=yyleng;}
\n { yycolumnO = yycolumn = 1; }

main {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_MAIN; }
int {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_INT; }
boolean {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_BOOLEAN; }
array {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_ARRAY; }
function {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_FUNCTION; }
if {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_IF; }
else { yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_ELSE; }
while {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_WHILE; }
scanf { yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_SCANF; }
printf {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_PRINTF; }
return 	{  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_RETURN; }
true 	{  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_TRUE; }
false {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_FALSE; }
";" {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_PUNTOYCOMA; }
"," {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_COMA; }
"(" { yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_PARENTESISIZQUIERDO; }
")" {yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_PARENTESISDERECHO; }
"[" { yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_CORCHETEIZQUIERDO; }
"]" { yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_CORCHETEDERECHO; }
"{" {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_LLAVEIZQUIERDA; }
"}" {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_LLAVEDERECHA; }
"=" {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_ASIGNACION; }
"+" {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_MAS; }
"-" {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_MENOS; }
"/" {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_DIVISION; }
"*" { yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_ASTERISCO; }
"&&" {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_AND; }
"||" { yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_OR; }
"!" {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_NOT; }
"==" { yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_IGUAL; }
"!=" {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_DISTINTO; }
"<=" { yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_MENORIGUAL; }
">=" {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_MAYORIGUAL; }
"<" { yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_MENOR; }
">" {  yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); return TOK_MAYOR; }

[a-zA-Z][a-zA-Z0-9]{100,} { fprintf( stderr, "****Error morfologico en [lin %d, col %d]: identificador demasiado largo (%s)\n", yylineno, yycolumn, yytext); tipoError = 1; return TOK_ERROR; }

[a-zA-Z][a-zA-Z0-9]* { yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); strcpy(yylval.atributos.lexema, yytext); return TOK_IDENTIFICADOR; }

[0-9]+ { yycolumn += yyleng; fprintf( outF, ";D:	%s\n", yytext ); yylval.atributos.valor_entero = atoi(yytext); yylval.atributos.tipo = 1; return TOK_CONSTANTE_ENTERA; }

. { fprintf( stderr, "****Error morfologico en [lin %d, col %d]: simbolo no permitido (%s)\n", yylineno, yycolumn, yytext); tipoError = 2; return TOK_ERROR; }

%%
