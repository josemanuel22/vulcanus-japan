
#ifndef PATH_H
#define PATH_H

#include <vector> 
#include <iostream>
#include <algorithm>


/**
 * Template class to compare two path.
 * A, B two path. A<B sii A weight < B weight
 * if A weight = B weight then A<N sii A length < B length.
 * Not a total order!
 */
template<class T> class weightLess{
	public:
		// Determine priority.
		bool operator()(const T& a, const T& b) const {
			if( a.getWeight() == b.getWeight() )
				return a.length() < b.length();
			return a.getWeight() < b.getWeight();
		}

		bool operator()(const T* a, const T* b) const {
			if( a->getWeight() == b->getWeight() )
				return a->length() < b->length();
			return a->getWeight() < b->getWeight();
		}
};

/**
 * Class that represent a path of vertex.
 */
class Path {
	protected:

		int Path_length; 							//N° of Vertex of the path
		int Path_weight;							//Weight of the path
		std::vector<int> Path_vertexList;			//List of Vertex that conform the path.

	public:

		Path();
		Path(const std::vector<int>& l, double w);
		~Path(void){}

		/**
		 * @return total weight of the path.  
		 */
		int getWeight() const;

		/**
		 * @param total weight of the path.  
		 */
		void setWeight(int val);

		/**
		 * @return total lenght of the path.  
		 */
		int length() const;

		/**
		 * @return the same path that this with the vertexList reversed.
		 */		
		Path* reverse() {std::reverse(Path_vertexList.begin(), Path_vertexList.end());return this;}

		/**
		 * @return the i-th vertex of the path.
		 */		
		int getVertex(int i);

		/**
		 * @param path to concatenated with.
		 * Concatenate this Path with the one given as argument.
		 * This is after the call the new Paht result of the concatenation
		 */
		void concat(Path* path);

		/**
		 * @param sub_path list of vertex where we will find the subpath of this that end in endingVertex 
		 * @param endingVertex endvertex of the subpath.
		 * Get the subpath of this that end-up in endingVertex.(not included!)
		 */
		bool subPath(std::vector<int>& sub_path, int endingVertex);

 		/**
 		 * @param v vertex to add
 		 * @param w weight of the vertex that start from the last node of the path and end in the new vertex. 
 		 * @param c resources cosumed by addind this new edge.
 		 * Add a vertex to the path. (so a edge that goes from the last vertex of the path and end-up in the new node added)
 		 */
		void addVertex(int v, double w);
		
		/**
		 * Print a simple path in the standart output.
		 */
		void printPath();
};



#endif
