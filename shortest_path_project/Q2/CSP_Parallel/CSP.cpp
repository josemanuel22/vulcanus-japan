

#include <iostream>
#include <fstream>
#include <set>
#include <vector>
#include <climits>
#include <math.h>
#include <mpi.h>
#include "Utils.hpp"
#include "Path.hpp"
#include "Dijkstra.hpp"
#include "Graph.hpp"

/**
 * A good explication of the Yen'salgorithm is given in wikipedia.
 * @see https://en.wikipedia.org/wiki/Yen's_algorithm.
 */
using namespace std;

Graph* graph;								// Our graph!

std::multiset<Path, weightLess<Path> > B;	// Set that hold the potential k-shortest path. Bis a container.
											// weightLess<Path> is given so the firts path in the set is the weightless one. 
std::vector<Path> A;						// A will hold the k-shortest path.

std::vector<int> Pth;						//Container used in the communication between process.

int fininish = 0;							// boolean so the process master indicates to the others that the computation is finished.


/**
 * @param me id of the current node of the process.
 * @param size n° of process in the computation
 * Indicates to the other process if the computation is finish
 * sending to them his fininish var. if fininish equals to 1 the comp is finished
 * 									 if not fininish equals to 0.
 */
void isFininish(int me, int size) {
	int tag = 312547; // tag that indicates that message send-recv is a isFininish message. 
	if(me == 0) { // the process master 0, send to the other process a message fininish to indicate the end or not. 
		for(int i =0; i<size; i++) {
			MPI_Send(&fininish, 1, MPI_INT, i, tag, MPI_COMM_WORLD);
		}
	} else {
		MPI_Recv(&fininish, 1, MPI_INT, 0, tag, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
	}
}

/**
 * @param path the path to broadcast from the master node 0 to the other nodes 
 * and push_back in the set A of each processors.
 * @param me the current process in the computation
 * @param size the total n° of process in the computation.
 * Send the path from 0 to all the other process the sets A of the computation. 
 */
void broacastA(Path* path,int me, int size) {
	int vertex,w;
	int tag = 0;
	int sendlengh = 123456; // tag to indicate that we send the length of the path.  
	int sendweight= 12345; 	// tag to indicate that we send the weight of the path. 
	int v, l;
	MPI_Request request;


	Pth.clear();
	if(me==0) { // the process 0 send the path
		vector<int> sub_path = path->getVertexList();
		l = sub_path.size();
		for(int i = 1; i<size; i++) { // process 0 send the path to all the other process in the computation
			MPI_Send(&l, 1, MPI_INT, i, sendlengh, MPI_COMM_WORLD); // send the length
			tag=0;
			for(vector<int>::iterator e = sub_path.begin(); e!=sub_path.end(); ++e) { //send each vertex of the path
				vertex = *e;
				MPI_Send(&vertex,1,MPI_INT,i, tag,MPI_COMM_WORLD);
				tag++;	
			}
			w = path->getWeight();
			MPI_Send(&w,1, MPI_INT,i, sendweight,MPI_COMM_WORLD); //send the weight.
		}
	} else {	// the others process receive these informations and rebuilt the path.

		MPI_Recv(&l, 1, MPI_INT, 0, sendlengh, MPI_COMM_WORLD, MPI_STATUS_IGNORE); 	//recv the lenght
		for(int t = 0; t<l; t++) {
			MPI_Recv(&v, 1, MPI_INT, 0, t, MPI_COMM_WORLD, MPI_STATUS_IGNORE); 		// recv vertex of the path.
			Pth.push_back(v);
		}
		MPI_Recv(&w, 1, MPI_INT, 0, sendweight, MPI_COMM_WORLD, MPI_STATUS_IGNORE);	//recv the weight.
		A.push_back(Path(Pth, w, graph->getRessouce(Pth))); // rebuilt the path and push_back in A 
		Pth.clear();
	}			 
}

/**
 * @param path the path to send from the master node 0 to the other nodes of the computation
 * @param org the current process in the computation
 * @param size the total n° of process in the computation.
 * Send the path from the master node 0 to the other 
 */
void removePath(Path* path,int org, int size) {
	int vertex,w;
	int tag =0;
	int sendlengh = 123456; // tag to indicate that we send the length of the path.  
	int sendweight= 12345;	// tag to indicate that we send the weight of the path.
	int v, l;
	MPI_Request request;

	Pth.clear();
	if(0==org) {	// the process 0 send the path
		vector<int> sub_path = path->getVertexList();
		l = sub_path.size();
		for(int i = 1; i<size; i++) { // process 0 send the length of the path to all the other process in the computation
			MPI_Send(&l, 1, MPI_INT, i, sendlengh, MPI_COMM_WORLD);	// send the length
			tag=0;
			for(vector<int>::iterator e = sub_path.begin(); e!=sub_path.end(); ++e) {
				vertex = *e;
				MPI_Send(&vertex,1,MPI_INT,i,tag,MPI_COMM_WORLD);
				tag++;	
			}
			w = path->getWeight();
			MPI_Send(&w,1, MPI_INT,i,sendweight,MPI_COMM_WORLD);	//send the weight.
		}
	} else {	// the others process receive these informations and rebuilt the path.
		MPI_Recv(&l, 1, MPI_INT, 0, sendlengh, MPI_COMM_WORLD, MPI_STATUS_IGNORE);	//recv the lenght
		for(int t = 0; t<l; t++) {
			MPI_Recv(&v, 1, MPI_INT, 0, t, MPI_COMM_WORLD, MPI_STATUS_IGNORE);		// recv vertex of the path.
			Pth.push_back(v);
		}
		MPI_Recv(&w, 1, MPI_INT, 0, sendweight, MPI_COMM_WORLD, MPI_STATUS_IGNORE);	//recv the weight.
		Path p(Pth, w, graph->getRessouce(Pth)); // rebuilt the path and we erase it from B.
		B.erase(p);
	}
}

/**
 * @param path the path to send
 * @param me the current process in the computation
 * @param dest destination node
 * @param size the total n° of process in the computation.
 * Send the path from the master process 0 to all the other ones
 */
void sendrecvPath(Path* path,int me ,int dest, int size) {
	int vertex,w;
	int tag =0;
	int sendlengh = 123456;
	int sendweight= 12345;
	int v, l;
	MPI_Request request;

	Pth.clear();
	if(me!=dest) {	// the process send the path
		vector<int> sub_path = path->getVertexList();
		l = sub_path.size();
		MPI_Send(&l, 1, MPI_INT, dest, sendlengh, MPI_COMM_WORLD); // send the length
		tag = 0;
		for(vector<int>::iterator e = sub_path.begin(); e!=sub_path.end(); ++e) { // process 0 send the path to all the other process in the computation vertex by vertex 
			vertex = *e;
			MPI_Send(&vertex,1,MPI_INT,dest,tag,MPI_COMM_WORLD); 
			tag++;	
		}
		w = path->getWeight();
		MPI_Send(&w,1, MPI_INT,dest,sendweight,MPI_COMM_WORLD);	//send the weight.
	} else {	// the others process receive these informations and rebuilt the path.
		for(int i=1; i<size; i++) {
			MPI_Recv(&l, 1, MPI_INT, i, sendlengh, MPI_COMM_WORLD, MPI_STATUS_IGNORE); 	//recv the lenght
			for(int t = 0; t<l; t++) {
				MPI_Recv(&v, 1, MPI_INT, i, t, MPI_COMM_WORLD, MPI_STATUS_IGNORE);		// recv vertex of the path.
				Pth.push_back(v);
			}
			MPI_Recv(&w, 1, MPI_INT, i, sendweight, MPI_COMM_WORLD, MPI_STATUS_IGNORE);	//recv the weight.
			B.insert(Path(Pth, w, graph->getRessouce(Pth))); // rebuilt the path and we insert it in B.
			Pth.clear();
		}	
	}		 
}


/**
 * Function that run's the Constrained shortest path parallel version.
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
 * @param me the current node in the computation
 * @param size the total n° of process in the computation
 */

 /**
  * The process 0 will be the master node. He will regroup information updating and resending it.
  *	Each process calculate a group of k-shortest potential path. Then send the result to the node 0
  * who calculate the min. Remove it from B and send to the other process which path to remove from, and add to their A. 
  */
void CSP(int source, int target, int R, int me, int size) { 

	// We get the shortest path of the graph from the source to the target. 
	// If it checks the constrain we have finished. 
	Path k_path;
	Dijkstra dijkstra(graph);
	k_path =*(dijkstra.getShortestPath(source, target));
	if(k_path.getRessource() < R && me == 0) {
		cout<<"Solution:"<<endl;
		k_path.printPath();
		fininish = 1;
	}
	isFininish(me, size); // we send that we have finished or not to all the other nodes.
	if(fininish == 1) {
		return;
	}

	//If the shortest path do not check the constrain we apply the yen algorithm until we find one.
	A.push_back(k_path);
	int k = 0;
	while(true){
		k++;
		int h = (int) ceil((double)(A[k-1].length()-1)/(double)size); 	// We divided the computation so each process calculate a proportional part.
		for(int i = me*h ; i<(me+1) *h && i<A[k-1].length()-1; i++) {	// The spur node ranges from the first node to the next to last node in the previous k-shortest path.
			int spurNode = A[k-1].getVertex(i);					// Spur node is retrieved from the previous k-shortest path, k − 1.
			std::vector<int> rootPath;							// The sequence of nodes from the source to the spur node of the previous k-shortest path
			A[k-1].subPath(rootPath, spurNode);							
			Path _rootPath_(rootPath, graph->getCost(rootPath), graph->getRessouce(rootPath));
			//_rootPath_.printPath();	
			for(std::vector<Path>::iterator p = A.begin(); p != A.end(); ++p) {
				std::vector<int> currSubPath;
				p->subPath(currSubPath, spurNode);
				if(rootPath == currSubPath) {	// Remove the links that are part of the previous shortest paths which share the same root path.
					graph->removeEdg(spurNode, p->getVertex(i+1));
				}
			}
			
			for(int rootPathNode = 0; rootPathNode<rootPath.size()-1; ++rootPathNode) {
				graph->removeNode(rootPath[rootPathNode]);
 			}
 			Path* spurPath;
 			spurPath = dijkstra.getShortestPath(spurNode, target);	// Calculate the spur path from the spur node to the target.				
 			if(spurPath!=NULL) {	// Entire path is made up of the root path and spur path.
 				_rootPath_.concat(spurPath);
 				B.insert(_rootPath_);	// Add the potential k-shortest path to the heap.
 			}

 			graph->restoreEdges();	// Add back the edges and nodes that were removed from the graph.
		}

		if(me!=0) {	//Each process send their potential k-shortest path that their just have calculated to the master node 0.
			for(multiset<Path, weightLess<Path> >::iterator e = B.begin(); e!=B.end(); e++) {
	 			Path path = *e;
	 			sendrecvPath(&path, me , 0, ((A[k-1].length()-1)>size) ? size : (A[k-1].length()-1));
	 		}
 		} else { //The process 0 recv those paths and added to his B
 			sendrecvPath(NULL, me , 0, ((A[k-1].length()-1)>size) ? size : (A[k-1].length()-1));
 		}

 		MPI_Barrier(MPI_COMM_WORLD);
 		if(me==0) {
 			if( B.empty()) { 
 				// This handles the case of there being no spur paths, or no spur paths left.
            	// This could happen if the spur paths have already been exhausted (added to A), 
            	// or there are no spur paths at all - such as when both the source and sink vertices 
            	// lie along a "dead end".
 				cout<<"No solution found"<<endl;
 				fininish = 1;
 				isFininish(me, size);
				return;
			}

			k_path = *(B.begin());
			if(k_path.getRessource() < R) { // If the k-shortest path found check the constrain we finish.
				cout<<"Solution:"<<endl;
				k_path.printPath();
				fininish = 1;
 				isFininish(me, size);
				return;
			}
			isFininish(me, size); 	//Have we finish? If yes all the process end.
			A.push_back(k_path);  	//Push_back the k-path to A
			removePath(&k_path, me, size);	//Remove the k-path from all the B sets
			broacastA(&k_path, me, size);	// Update all A of each process.
			B.erase(k_path);				// Erase the k-path from his B set
		} else {
			isFininish(me, size);
			if(fininish==1) { //if flag finish is up we end.
				return;
			}
			removePath(NULL, me, size); //Remove the k-path from all the B sets
			broacastA(NULL, me, size);	// Update all A of each process.
		}
		MPI_Barrier(MPI_COMM_WORLD);
	}
	MPI_Barrier(MPI_COMM_WORLD);
}

int main(int argc, char** argv) {
	
	int me, size;
	//Init MPI
	MPI_Init(&argc, &argv);
  	MPI_Comm_rank(MPI_COMM_WORLD, &me);
  	MPI_Comm_size(MPI_COMM_WORLD, &size);
  	graph = new Graph("Tests/rcsp1.txt");

	int R;
	int source, target;
	int sendSource = 1994;
	int sendTarget = 1995;
	int sendRessource = 1996;
	if(me == 0) {
		cout<<"Enter source node: ";
		cin>>source;
		for(int i =1; i<size; i++) {
			MPI_Send(&source, 1, MPI_INT, i, sendSource, MPI_COMM_WORLD);
		}
		
		cout<<"Enter target node: ";
		cin>>target;
		for(int i =1; i<size; i++) {
			MPI_Send(&target, 1, MPI_INT, i, sendTarget, MPI_COMM_WORLD);
		}
		cout<<"Enter resource max consumed: ";
		cin>>R;
		for(int i =1; i<size; i++) {
			MPI_Send(&R, 1, MPI_INT, i, sendRessource, MPI_COMM_WORLD);
		}

	} else {
		MPI_Recv(&source, 1, MPI_INT, 0, sendSource, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
		MPI_Recv(&target, 1, MPI_INT, 0, sendTarget, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
		MPI_Recv(&R, 1, MPI_INT, 0, sendRessource, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
	}	
	CSP(source,target, R, me, size);
	MPI_Finalize();
}


