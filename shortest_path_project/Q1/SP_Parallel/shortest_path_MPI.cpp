

// node 0 will both participate in the computation and serve as a
// "manager"

#include <mpi.h>
#include <iostream>
#include <fstream>
#include <new>
#include <vector>

#define MYMIN_MSG 0
#define OVRLMIN_MSG 1
#define COLLECT_MSG 2
#define DBG 1
#define MAXINT 10000
#define UNDEFINED -1

using namespace std;

// global variables (but of course not shared across nodes)

int nv, 					// number of vertices
	*notdone, 				// vertices not checked yet
	nnodes, 				// number of MPI nodes in the computation
	chunk, 					// number of vertices handled by each node
	startv,endv, 			// start, end vertices for this node
	me; 					// my node number

unsigned 	largeint, 		// max possible unsigned int
			mymin[2], 		// mymin[0] is min for my chunk,
							// mymin[1] is vertex which achieves that min
			othermin[2], 	// othermin[0] is min over the other chunks
							// (used by node 0 only)
							// othermin[1] is vertex which achieves that min
			overallmin[2], 	// overallmin[0] is current min over all nodes,
							// overallmin[1] is vertex which achieves that min
			*ohd, 			// 1-hop distances between vertices; "ohd[i][j]" is
							// ohd[i*nv+j]
			*mind; 			// min distances found so far

int *previous, finish = 0;
int target, source, p;

double T1,T2;				// start and finish times


/*
 * Initializes MPI, reads the file where the graph is represented and stores the informations  
 * in the data strutures.
 */
void init(int ac, char **av, int source) {


	// We read the file where the graph is represented and we initialized the structures.

	ifstream file;
	file.open(av[1]);
	int m;											//Number of edges
	int nr;         								//Number of resources
	int lr, ur;     								//lower limit & upper limit on the resources consumed on the chosen path

	file>>nv>>m>>nr;
    file>>lr>>ur;

    chunk = nv/nnodes;
	startv = me * chunk;
	endv = startv + chunk - 1;
	largeint = MAXINT;

	ohd =  new unsigned int [nv*nv];	//Adjiacent list 
	mind = new unsigned int [nv];
	notdone = new int [nv];
	previous = new int [nv];

	int aux;
    for(int i=0;i<nv; i++) {
        file>>aux;
    }
 
    for(int i = 0; i<nv*nv; i++) {
    	ohd[i] = largeint;
    }

    int x,y,w,c;
    for(int i=0;i<m;i++) { //Building Graph
        file>>x>>y>>w>>c; //Vertex1, Vertex2, weight of edge
        ohd[nv*(x-1)+(y-1)] = w;
    }
 
    for (int i = 0; i < nv; i++) {
		notdone[i] = 1;
		mind[i] = largeint;
		previous[i] = UNDEFINED;
	}
	mind[source-1] = 0;		// The source is at distance 0						
}


// finds closest to source among notdone, among startv through endv
void findmymin() {
	mymin[0] = largeint;
	for (int i = startv; i <= endv; i++) {
		if (notdone[i] && mind[i] < mymin[0]) {
			mymin[0] = mind[i];
			mymin[1] = i;
		}
	}
}

void findoverallmin(int source, int target) { 
	int i;
	MPI_Status status; // describes result of MPI_Recv() call
	// nodes other than 0 report their mins to node 0, which receives
	// them and updates its value for the global min
	if (me > 0) {
		MPI_Send(mymin,2,MPI_INT,0,MYMIN_MSG,MPI_COMM_WORLD);
	 } else {
		// check my own first
		overallmin[0] = mymin[0];
		overallmin[1] = mymin[1];
		// check the others
		for (i = 1; i < nnodes; i++) {
			MPI_Recv(othermin,2,MPI_INT,i,MYMIN_MSG,MPI_COMM_WORLD,&status);
			if (othermin[0] < overallmin[0]) {
				overallmin[0] = othermin[0];
				overallmin[1] = othermin[1];
			}
		}
	}
}

void updatemymind() { // update my mind segment
	// for each i in [startv,endv], ask whether a shorter path to i
	// exists, through mv
	int i, mv = overallmin[1];
	unsigned md = overallmin[0];
	for (i = startv; i <= endv; i++) {
		if (md + ohd[mv*nv+i] < mind[i]) {
			mind[i] = md + ohd[mv*nv+i];
			previous[i] = (mv+1);
		}
	}
}


void disseminateoverallmin() {
	int i;
	MPI_Status status;
	if (me == 0)
		for (i = 1; i < nnodes; i++)
			MPI_Send(overallmin,2,MPI_INT,i,OVRLMIN_MSG,MPI_COMM_WORLD);
	else
		MPI_Recv(overallmin,2,MPI_INT,0,OVRLMIN_MSG,MPI_COMM_WORLD,&status);
}


void updateallmind() {// collects all the mind segments at node 0
	int i;
	MPI_Status status;
	if (me > 0) {
		MPI_Send(mind+startv,chunk,MPI_INT,0,COLLECT_MSG,MPI_COMM_WORLD);
		MPI_Send(previous+startv, chunk, MPI_INT, 0, COLLECT_MSG, MPI_COMM_WORLD);
	} else {
		for (i = 1; i < nnodes; i++) {
			MPI_Recv(mind+i*chunk,chunk,MPI_INT,i,COLLECT_MSG,MPI_COMM_WORLD,&status);
			MPI_Recv(previous+i*chunk,chunk,MPI_INT,i,COLLECT_MSG,MPI_COMM_WORLD,&status);
		}
	}
}


void printmind() { // partly for debugging (call from GDB)
	cout<<"minimum distances: "<<endl;
	for (int i = 0; i < nv; i++)
		cout<<mind[i]<<endl;
}

void dowork(int source, int target) {
	if (me == 0) T1 = MPI_Wtime();
	for (int step = 0; step < nv; step++) {
		findmymin();
		findoverallmin(source, target);
		disseminateoverallmin();
		/*if(overallmin[1] == target-1) {
			break;
		}*/
		// mark new vertex as done
		notdone[overallmin[1]] = 0;
		updatemymind();

	}
	updateallmind();
	T2 = MPI_Wtime();
}


int main(int ac, char **av) {

	int sendSource = 1994;
	int sendTarget = 1995;

	if(ac<2) {
		cout<<"Il faut indiquer le nom la route du fichier contenant le graphe Ex: ./shortest_path_seq ./Tests/rcsp1.txt"<<endl;
        return 0;
	}

	MPI_Init(&ac,&av); 								//Ini MPI
	MPI_Comm_size(MPI_COMM_WORLD,&nnodes); 			//N° Nodes
	MPI_Comm_rank(MPI_COMM_WORLD,&me);				//N° of the current Node*/

	if(me ==0) {
		cout<<"Introduire le noeud source: ";
		cin>>source;
		for(int i =1; i<nnodes; i++) {
			MPI_Send(&source, 1, MPI_INT, i, sendSource, MPI_COMM_WORLD);
		}
		cout<<"Introduire le noeud final: ";
		cin>>target;
		for(int i =1; i<nnodes; i++) {
			MPI_Send(&target, 1, MPI_INT, i, sendTarget, MPI_COMM_WORLD);
		}
	}else {
		MPI_Recv(&source, 1, MPI_INT, 0, sendSource, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
		MPI_Recv(&target, 1, MPI_INT, 0, sendTarget, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
	}

	p=target;
	init(ac, av, source);
	dowork(source, target);

	if (me == 0) {
    	cout<<target<<"<-";
    	while((p=previous[p-1]) != source) {
    		cout<<p<<"<-";
    		if(p == 0) 
    			break;
    	}	
    	cout<<source<<endl;
		cout.flush();
	}
	if (me == 0) cout<<"time at node 0: "<<(float)(T2-T1)<<endl;
	MPI_Finalize();
}


