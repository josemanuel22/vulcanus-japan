

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

std::multiset<Path, weightLess<Path> > B;	// Set that hold the potential k-shortest path. Bis a container.
											// weightLess<Path> is given so the firts path in the set is the weightless one. 
std::vector<Path> A;						// A will hold the k-shortest path.


/**
 * Function that run's the K-shortest paths.
 * It use the yen algorithm to enumerated the shortest paths.
 * The solution is O(KN(M+N log N)). If B is a Fibonaci heap! Like is the case of a multiset.
 *
 * @param graph where to find the constrained shortest path.
 * @param source node
 * @param target node
 * @param K the number of shortest-path we want to find.
 */

void YenKSP(Graph* graph, int source, int target, int K) {

	// Determine the shortest path from the source to the target
	Path k_path;
	Dijkstra dijkstra(graph);
	k_path =*(dijkstra.getShortestPath(source, target));
	A.push_back(k_path);


	for(int k = 1 ; k<K; k++) {
		// The spur node ranges from the first node to the next to last node in the previous k-shortest path.
		for(int i = 0 ; i<A[k-1].length()-1; i++) {
			int spurNode = A[k-1].getVertex(i); // Spur node is retrieved from the previous k-shortest path, k − 1.
			std::vector<int> rootPath; 			// The sequence of nodes from the source to the spur node of the previous k-shortest path.
			A[k-1].subPath(rootPath, spurNode);
			Path _rootPath_(rootPath, graph->getCost(rootPath));	
			for(std::vector<Path>::iterator p = A.begin(); p != A.end(); ++p) {
				std::vector<int> currSubPath;
				p->subPath(currSubPath, spurNode);
				if(rootPath == currSubPath) {  // Remove the links that are part of the previous shortest paths which share the same root path.
					graph->removeEdg(spurNode, p->getVertex(i+1));
				}
			}
			
			for(int rootPathNode = 0; rootPathNode<rootPath.size()-1; ++rootPathNode) {
				graph->removeNode(rootPath[rootPathNode]);
 			}
 			Path* spurPath;
 			spurPath = dijkstra.getShortestPath(spurNode, target);	// Calculate the spur path from the spur node to the sink.	
 			if(spurPath!=NULL) { 
 				_rootPath_.concat(spurPath);// Entire path is made up of the root path and spur path.
 				B.insert(_rootPath_);		// Add the potential k-shortest path to the heap.
 			}
 			// Add back the edges and nodes that were removed from the graph.
 			graph->restoreEdges();
		}

		if( B.empty()) {
			// This handles the case of there being no spur paths, or no spur paths left.
          	// This could happen if the spur paths have already been exhausted (added to A), 
            // or there are no spur paths at all - such as when both the source and sink vertices 
           	// lie along a "dead end".
			break;
		}
		k_path = *(B.begin());
		A.push_back(k_path); // Add the lowest cost path becomes the k-shortest path.
		B.erase(k_path);
	}
}

int main() {

	Graph graph("Tests/rcsp1.txt");
	int K;
	int source, target;
	cout<<"Enter source node: ";
	cin>>source;
	cout<<"Enter target node: ";
	cin>>target;
	cout<<"Enter K: ";
	cin>>K;
	YenKSP(&graph, source, target, K);

	for(int i=0; i<A.size(); i++) {
		A[i].printPath();
	}

}


