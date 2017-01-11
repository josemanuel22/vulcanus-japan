
CC = mpic++
FLAGS = -std=c++11
EXE = CSP

all: $(EXE)

$(EXE): 
	$(CC) $(FLAGS) CSP.cpp Dijkstra.cpp Graph.cpp Path.cpp -o $(EXE)
	
clean:
	rm -f *.o
	rm -f $(EXE)
