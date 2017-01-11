#include "assert.h"
#include "../Path.cpp"
#include <iostream>

int main() {
	Path path1;

	std::vector<int> vertex;
	for(int i =0; i<10; i++) {
		vertex.push_back(i);
	}
	int weight =10;
	Path path2(vertex, weight);

	assert(path2.length() == 10);
	assert(path2.getWeight() == weight);

	std::vector<int> subpath;

	path2.subPath(subpath,7);
	assert(subpath.size() == 7);

	for(int i = 0; i<7; i++) {
		path1.addVertex(i, 1);
	}
	assert(path1.getWeight() == 7);
	assert(path1.length() == 7);

	std::vector<int> subpath2;
	path1.subPath(subpath2, 7);
	assert(subpath2 == subpath);

	cout<<"Success"<<endl;
}