
#include "Path.hpp"
using namespace std;

Path::Path() {
	Path_length = 0;
	Path_weight = 0;
	Path_ressource = 0;
}

Path::Path(const vector<int>& l, double w, double r) {
	Path_vertexList.assign(l.begin(), l.end());
	Path_length = Path_vertexList.size();
	Path_weight= w;
	Path_ressource=r;
}

int Path::getWeight() const { return Path_weight; }
void Path::setWeight(int val) { Path_weight = val; }
int Path::length() const { return Path_length; }

int Path::getRessource() const {return Path_ressource;}
void Path::setRessource(int val) {Path_ressource = val;}

int Path::getVertex(int i) {
	return Path_vertexList.at(i);

}

bool Path::subPath(vector<int>& sub_path, int endingVertex) {
	for (vector<int>::iterator pos = Path_vertexList.begin(); pos != Path_vertexList.end(); ++pos) {	
		if (*pos != endingVertex) {
			sub_path.push_back(*pos);
		} else {
			sub_path.push_back(endingVertex);
			return true;
		}
	}
	return false;
}

void Path::concat(Path* path) {
	Path_vertexList.insert(Path_vertexList.end(), (++path->Path_vertexList.begin()), path->Path_vertexList.end());
	Path_length +=(path->Path_length)-1;
	Path_weight+=(path->Path_weight);
	Path_ressource+=path->Path_ressource;
}

void Path::addVertex(int v, double w, double c) {
	Path_vertexList.push_back(v);
	Path_length++;
	Path_weight+=w;	
	Path_ressource+=c;
}

void Path::printPath() {
	cout<<"Cost: "<<getWeight()<<" lenght: "<<length()<<" cost: "<<getRessource()<<endl;
	for(int i = 0; i<Path_vertexList.size()-1; i++) {
		cout<<Path_vertexList[i]<<"->";
	}
	cout<<Path_vertexList[Path_vertexList.size()-1]<<endl;
}

std::vector<int> Path::getVertexList() {
	return Path_vertexList;
}


