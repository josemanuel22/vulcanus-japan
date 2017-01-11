
#ifndef GRAPH_H
#define GRAPH_H


#include <iostream>
#include <fstream>
#include <vector>
#include <string>
#include "Path.hpp"
#include "Utils.hpp"


using namespace std;

/*Class that represent a Graph*/
class Graph {

	protected: 

		int vertex; 									//Number of vertices
		int edges;										//Number of edges
		int nOfRessources;								//Number of resources
		int lr, ur; 									//lower limit & upper limit on the resources consumed on the chosen path

		vector<pair<int,int> > a[MAX_SIZE]; 			//Adjacency list. The edge whom go from i to j is: a[i] and then pair (j,w) of the vector. w is the weight.
		vector<pair<int,int> > r[MAX_SIZE]; 			//map edge with resource


		vector<pair<int,pair<int, int> > > rmvEdges;	//List that contain the removed edge. we store in the vector pairs like: (i,(j,w))



	public:
		// Constructors and Destructor
		Graph(const string& input_file_name);
		~Graph(void) {};

		/**
		 * @return nunmber of edges of the graph.  
		 */
		int getnEdges() {return edges;}

		int getEdgDest( int i, int j);

		/**
		 * @param vertex origin i, vertex target j.
		 * @return Cost of the edge.
		 */
		int getEdgCost( int i, int j);

		/**
		 * @param vertex origin i, vertex target j.
		 * @return Ressource consumed of the edge.
		 */
		int getEdgRessource( int i, int j);

		/**
		 * @param l list of vertex of a path.
		 * @return The total cost of this path.
		 */
		int getCost(std::vector<int>& l);

		/**
		 * @param l list of vertex of a path.
		 * @return The total ressources consumed by using this path.
		 */
		int getRessouce(std::vector<int>& l);

		/**
		 * @param vertex i.
		 * @return a list with the neighbour and there weight.
		 */
		std::vector<std::pair<int, int> > getNeighbour(int i);

		/**
		 * @param vertex i
		 * Remove the node from the graph.
		 * Removing a node is the same that removing all the edges that start on him and finish on him
		 */
		void removeNode(int i);

		/**
		 *@param vertex i, vertex j
		 * Remove all the edge that start in vertex i and finish in vertex j.
		 * The remove vertex is store in rmvEdges.
		 */
		void removeEdg( int i, int j);

		/**
		 * Restore all the edges remove. So restore the edges that are in rmvEdges
		 */
		void restoreEdges();

	friend class Dijkstra;	/* The class Dijkstra whom calculated the shortest path, can access to protected and private members of the graph */

};

#endif