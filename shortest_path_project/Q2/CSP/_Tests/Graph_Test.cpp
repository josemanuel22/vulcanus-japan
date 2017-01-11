
#include "assert.h"
#include "../Graph.cpp"
#include <iostream>
#include <string>

int main() {


	Graph graph("../Tests/my_test.txt");

	assert(graph.getnEdges() == 7);

	assert(graph.getEdgCost(4,5)==1);
	assert(graph.getEdgCost(1,4)==4);
	assert(graph.getEdgRessource(1,4) == 72);
	assert(graph.getNeighbour(1)[0].first == 2);
	assert(graph.getNeighbour(1)[0].second == 1);
	assert(graph.getNeighbour(3)[0].first == 6);
	assert(graph.getNeighbour(3)[0].second == 1);

	std::vector<int> path;
	path.push_back(1); path.push_back(2); path.push_back(3); path.push_back(6);

	cout<<graph.getCost(path)<<endl;
	assert(graph.getRessouce(path)==216);
	path.clear();
	path.push_back(1);path.push_back(6);
	assert(graph.getRessouce(path)==INF);


	path.clear();
	path.push_back(1); path.push_back(2); path.push_back(4);path.push_back(5); path.push_back(6);
	cout<<graph.getCost(path)<<endl;

	for(int i = 1; i<=graph.getnEdges() ; i++) {
		std::vector<std::pair<int, int> > neighbour = graph.getNeighbour(i);
		for(int j=0; j<neighbour.size(); j++) {
			cout<<i<<" "<<neighbour[j].first<<" "<<neighbour[j].second<<endl; 
		}
	}

	graph.removeEdg(1, 4);
	cout<<graph.getEdgCost(1,4)<<endl;

	cout<<"Success"<<endl;	

}