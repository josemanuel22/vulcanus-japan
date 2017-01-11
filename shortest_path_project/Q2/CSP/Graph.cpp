

#include "Graph.hpp"



Graph::Graph(const string& input_file_name) {
	const char* file_name = input_file_name.c_str();

	ifstream file(file_name);
    
    int x,y,w,c;
    
    /* We read the file where the graph is represented and we stored in our data struture.*/
    if(!file) {
    	cerr << "The file " << file_name << " can not be opened!" << endl;
    	file.close();
    	return;
    }

    file>>vertex>>edges>>nOfRessources;
    file>>lr>>ur;
    int aux;
    for(int i=0;i<vertex; i++) {
        file>>aux;
    } 

    
    for(int i=0;i<edges;i++) { //Building Graph
        file>>x>>y>>w>>c; //Vertex1, Vertex2, weight of edge
        a[x].push_back(make_pair(y,w));
        r[x].push_back(make_pair(y,c));
    }
    file.close();
}

int Graph::getCost(std::vector<int>& l) {
	int w=0;
	for(int i = 0; i<l.size()-1; i++) {
		w+=getEdgCost(l[i], l[i+1]);
	}
	return w;
}

int Graph::getRessouce(std::vector<int>& l) {
	int r=0;
	for(int i = 0; i<l.size()-1; i++) {
		r+=getEdgRessource(l[i], l[i+1]);
	}
	return r;
}

vector<pair<int, int> > Graph::getNeighbour(int i) {
	return a[i];
}

int Graph::getEdgDest(int i, int j) {
	int k=0;
	for(vector<pair<int, int> >::iterator e = a[i-1].begin(); e != a[i-1].end() ; ++e ) {
		if(e->first == j) {
			return a[i][j].first;
		}
		k++;
	}
	return -1;
}

int Graph::getEdgCost(int i, int j) {
	int k=0;
	for(vector<pair<int, int> >::iterator e = a[i].begin(); e != a[i].end() ; ++e) {
		if(e->first == j) {
			return a[i][k].second;
		}
		k++;
	}
	return INF;
}

int Graph::getEdgRessource( int i, int j) {
	for(vector<pair<int, int> >::iterator e = r[i].begin(); e != r[i].end() ; ++e) {
		if(e->first == j) {
			return e->second;
		}
	}
	return INF;
}

void Graph::removeNode(int i) {
	for(vector<pair<int, int> >::iterator e = a[i].begin(); e!=a[i].end(); ++e) {
		rmvEdges.push_back(make_pair(i,make_pair(e->first,e->second)));
	}
	a[i].clear();
}

void Graph::removeEdg( int i, int j) {
	for(vector<pair<int, int> >::iterator e = a[i].begin(); e != a[i].end() ; ++e ) {
		if(e->first == j) {
			rmvEdges.push_back(make_pair(i,make_pair(j,e->second)));
			a[i].erase(e);
			break;
		}
	}
}

void Graph::restoreEdges() {
	for(vector<pair<int, pair<int,int> > >::iterator e = rmvEdges.begin(); e!=rmvEdges.end(); ++e) {
		a[e->first].push_back(make_pair(e->second.first, e->second.second));
	}
	rmvEdges.clear();
}

