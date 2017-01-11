#include <vector>
#include <set>
#include <mpi.h>
#include <iostream>
#include "Utils.hpp"
#include "Path.cpp"

using namespace std;

std::multiset<Path, weightLess<Path> > B;
std::vector<Path> A;
std::vector<int> Pth;

void printer(vector<int> v) {
	for(int i=0; i<v.size()-1; i++) {
		cout<<v[i]<<"->";
	}
	cout<<v[v.size()-1]<<endl;
}

void broacastA(Path* path,int me, int size) {
	int vertex,w;
	int tag =0;
	int sendlengh = 123456;
	int sendweight= 12345;
	int v, l;
	MPI_Request request;

	if(me==0) {
		vector<int> sub_path = path->getVertexList();
		//printer(sub_path);
		l = sub_path.size();
		for(int i = 1; i<size; i++) {
			MPI_Send(&l, 1, MPI_INT, i, sendlengh, MPI_COMM_WORLD);
			tag=0;
			for(vector<int>::iterator e = sub_path.begin(); e!=sub_path.end(); ++e) {
				vertex = *e;
				MPI_Send(&vertex,1,MPI_INT,i, tag,MPI_COMM_WORLD);
				tag++;	
			}
			w = path->getWeight();
			MPI_Send(&w,1, MPI_INT,i, sendweight,MPI_COMM_WORLD);
		}
	} else {
		
		MPI_Recv(&l, 1, MPI_INT, 0, sendlengh, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
		for(int t = 0; t<l; t++) {
			MPI_Recv(&v, 1, MPI_INT, 0, t, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
			Pth.push_back(v);
			cout<<"hola"<<endl;
		}
		MPI_Recv(&w, 1, MPI_INT, 0, sendweight, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
		A.push_back(Path(Pth, w));
		Pth.clear();
	}			 
}

void removePath(Path* path,int org, int size) {
	int vertex,w;
	int tag =0;
	int sendlengh = 123456;
	int sendweight= 12345;
	int v, l;
	MPI_Request request;

	if(0==org) {
		//cout<<"hola"<<endl;
		vector<int> sub_path = path->getVertexList();
		//printer(sub_path);
		l = sub_path.size();
		for(int i = 1; i<size; i++) {
			MPI_Send(&l, 1, MPI_INT, i, sendlengh, MPI_COMM_WORLD);
			tag=0;
			for(vector<int>::iterator e = sub_path.begin(); e!=sub_path.end(); ++e) {
				vertex = *e;
				MPI_Send(&vertex,1,MPI_INT,i,tag,MPI_COMM_WORLD);
				tag++;	
			}
			w = path->getWeight();
			MPI_Send(&w,1, MPI_INT,i,sendweight,MPI_COMM_WORLD);
		}
	} else {
		MPI_Recv(&l, 1, MPI_INT, 0, sendlengh, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
		for(int t = 0; t<l; t++) {
			MPI_Recv(&v, 1, MPI_INT, 0, t, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
			Pth.push_back(v);
		}
		MPI_Recv(&w, 1, MPI_INT, 0, sendweight, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
		Path p(Pth, w);
		B.erase(p);
	}			 
}

void sendrecvPath(Path* path,int me ,int dest, int size) {
	int vertex,w;
	int tag =0;
	int sendlengh = 123456;
	int sendweight= 12345;
	int v, l;
	MPI_Request request;
	if(me!=dest) {
		vector<int> sub_path = path->getVertexList();
		//printer(sub_path);
		l = sub_path.size();
		MPI_Send(&l, 1, MPI_INT, dest, sendlengh, MPI_COMM_WORLD);
		for(vector<int>::iterator e = sub_path.begin(); e!=sub_path.end(); ++e) {
			vertex = *e;
			MPI_Send(&vertex,1,MPI_INT,dest,tag,MPI_COMM_WORLD);
			tag++;	
		}
		w = path->getWeight();
		MPI_Send(&w,1, MPI_INT,dest,sendweight,MPI_COMM_WORLD);
	} else {
		for(int i=1; i<size; i++) {
			MPI_Recv(&l, 1, MPI_INT, i, sendlengh, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
			for(int t = 0; t<l; t++) {
				MPI_Recv(&v, 1, MPI_INT, i, t, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
				Pth.push_back(v);
			}
			MPI_Recv(&w, 1, MPI_INT, i, sendweight, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
			B.insert(Path(Pth, w));
			Pth.clear();
		}	
	}		 
}

int main(int argc, char* argv[]) {

	int me, size;
	Path* p = NULL;
 
  	MPI_Init(&argc, &argv);
  	MPI_Comm_rank(MPI_COMM_WORLD, &me);
  	MPI_Comm_size(MPI_COMM_WORLD, &size);

	std::vector<int> l;
	int w = 10;
	for(int i =0; i<me+1; i++) {
		l.push_back(i);
	}
	//printer(l);
	Path* path = new Path(l, w);
	sendrecvPath(path,me ,0, size);

	if(me==0) {
		for(multiset<Path, weightLess<Path> >::iterator e = B.begin(); e!=B.end(); e++) {
			e->printPath();
		}
	}

	if(me==0) {
		Path k_path = *(B.begin());
		A.push_back(k_path);
		//B.erase(k_path);
		//cout<<A.size()<<" "<<me<<endl;
	}
	MPI_Barrier(MPI_COMM_WORLD);
	/*
	if(me == 0) {
		p = &A[0];
	}
	
	//cout<<"caracola"<<endl;
	//p->printPath();
	cout<<"N process: "<<me<<" Size A before brocast "<<A.size()<<endl;
	broacastA(p,me,size);
	if(me != 0) {
		cout<<"N process "<<me<<" A begin after broacast"<<endl;
		A.begin()->printPath();
	}
	
	*/
	
	if(me!=0) {
		B.insert(*path);
	}
	Path* p2 = NULL;
	if(me ==0)
		p2 = &A[0];
	removePath(p2,me, size);
	
	for(multiset<Path, weightLess<Path> >::iterator e = B.begin(); e!=B.end(); e++) {
		cout<<"------------"<<endl;
		cout<<me<<endl;
		e->printPath();
	}	

	MPI_Barrier(MPI_COMM_WORLD);
	MPI_Finalize();
	return 0;
}