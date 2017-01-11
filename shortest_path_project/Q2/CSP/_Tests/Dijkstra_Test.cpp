
#include "../Dijkstra.cpp"
#include "../Graph.cpp"
#include "../Path.cpp"

int main() {

	Graph graph("../Tests/rcsp1.txt");

	Dijkstra dijkstra(&graph);
	Path* p = dijkstra.getShortestPath(1,99);
	p->printPath();
	p = dijkstra.getShortestPath(56,33);
	p->printPath();

}
