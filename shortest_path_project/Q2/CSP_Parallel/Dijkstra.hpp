
#ifndef DIJKSTRA_H
#define DIJKSTRA_H

#include <queue>
#include <vector>
#include "Graph.hpp"
#include "Path.hpp"
#include "Utils.hpp"


/* 
 * Class for the calculation of the calculation of the shortest path 	
 * Dijkstra class is friend of Graph class.						
 */
class Dijkstra {

	private:
		Graph* graph; 						//Adjacency list
		int dis[MAX_SIZE]; 					//Stores shortest distance
		bool vis[MAX_SIZE]={0}; 			//Determines whether the node has been visited or not
		int previous[MAX_SIZE]; 			//previous vertex in the shortest path  leading to it.
		
		Path* makePath(int source, int target);	/*Wrapper Method: construct the shortest path after all the Dijkstra calculation.*/

	public:

		/**
		 *@param graph pointer to the graph where we are going to run Dijkstra algorithm
		 */
		Dijkstra(Graph* g);

		/**
		 *@param source node 
		 *@param target node
		 *Get the shortes path from the source to the target for the graph given in the constructor
		 */
		Path* getShortestPath(int source, int target);
	
};

#endif
