

#include <iostream>
#include <fstream>
#include <set>
#include <vector>
#include <climits>
#include "Utils.hpp"
#include "Path.hpp"
#include "Dijkstra.hpp"
#include "Graph.hpp"

/**
 * A good explication of the Yen'salgorithm is given in wikipedia.
 * @see https://en.wikipedia.org/wiki/Yen's_algorithm.
 */

using namespace std;

std::multiset<Path, weightLess<Path> > B; 	// Set that hold the potential k-shortest path. Bis a container.
											// weightLess<Path> is given so the firts path in the set is the weightless one. 
std::vector<Path> A;						// A will hold the k-shortest path.

/**
 * Function that run's the Constrained shortest path.
 * It use the yen algorithm to enumerated the shortest paths,
 * until it find the first one to satisfy the constrain.
 * The solution is exponential. However the problem is NP-Hard.
 * So there is no polynomial solution find yet!
 * @see https://smartech.gatech.edu/bitstream/handle/1853/28268/garcia_renan_200905_phd.pdf
 * (page 2 and 25-28) to see other possible approaches
 *
 * @param graph where to find the constrained shortest path.
 * @param source node
 * @param target node
 * @param R the constrain to respect.
 */

void CSP(Graph* graph, int source, int target, int R) {

	// We get the shortest path of the graph from the source to the target. 
	// If it checks the constrain we have finished. 
	Path k_path;
	Dijkstra dijkstra(graph);
	k_path =*(dijkstra.getShortestPath(source, target));
	if(k_path.getRessource() < R) {
		cout<<"Solution:"<<endl;
		k_path.printPath();
		return;
	}

	//If the shortest path do not check the constrain we apply the yen algorithm until we find one.
	A.push_back(k_path);
	int k = 0;
	while(true){
		k++;
		// The spur node ranges from the first node to the next to last node in the previous k-shortest path.
		for(int i = 0 ; i<A[k-1].length()-1; i++) {
			int spurNode = A[k-1].getVertex(i); 	// Spur node is retrieved from the previous k-shortest path, k − 1.
			std::vector<int> rootPath;				// The sequence of nodes from the source to the spur node of the previous k-shortest path
			A[k-1].subPath(rootPath, spurNode);							
			Path _rootPath_(rootPath, graph->getCost(rootPath), graph->getRessouce(rootPath));	
			for(std::vector<Path>::iterator p = A.begin(); p != A.end(); ++p) {
				std::vector<int> currSubPath;
				p->subPath(currSubPath, spurNode);
				if(rootPath == currSubPath) { 		// Remove the links that are part of the previous shortest paths which share the same root path.
					graph->removeEdg(spurNode, p->getVertex(i+1));
				}
			}
			
			for(int rootPathNode = 0; rootPathNode<rootPath.size()-1; ++rootPathNode) {
				graph->removeNode(rootPath[rootPathNode]);
 			}

 			Path* spurPath;
 			spurPath = dijkstra.getShortestPath(spurNode, target); // Calculate the spur path from the spur node to the target.		
 			if(spurPath!=NULL) { 		// Entire path is made up of the root path and spur path.
 				_rootPath_.concat(spurPath);
 				B.insert(_rootPath_); 	// Add the potential k-shortest path to the heap.
 			}

 			graph->restoreEdges(); // Add back the edges and nodes that were removed from the graph.
		}

		if( B.empty()) {
			// This handles the case of there being no spur paths, or no spur paths left.
            // This could happen if the spur paths have already been exhausted (added to A), 
            // or there are no spur paths at all - such as when both the source and sink vertices 
            // lie along a "dead end".
			cout<<"No solution found"<<endl;
			return;
		}

		k_path = *(B.begin());
		if(k_path.getRessource() < R) { // If the k-shortest path found check the constrain we finish.
			cout<<"Solution:"<<endl;
			k_path.printPath();
			return;
		}
		A.push_back(k_path); // Add the lowest cost path becomes the k-shortest path.
		B.erase(k_path);
	}
}

int main() {
	Graph graph("Tests/rcsp1.txt");
	
	int R;
	int source, target;
	cout<<"Enter source node: ";
	cin>>source;
	cout<<"Enter target node: ";
	cin>>target;
	cout<<"Enter resource max consumed: ";
	cin>>R;
	CSP(&graph,source, target, R);
}


