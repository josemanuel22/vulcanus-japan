
CC = mpic++
FLAGS = -std=c++11
EXE = Yen_algorithm

all: Yen_algorithm

$(EXE): 
	$(CC) $(FLAGS) Yen_algorithm.cpp Dijkstra.cpp Graph.cpp Path.cpp -o $(EXE)
	
clean:
	rm -f *.o
	rm -f $(EXE)