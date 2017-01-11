
#include "Dijkstra.hpp"
#include "Graph.hpp"


Dijkstra::Dijkstra(Graph* g) {
	graph = g;
}


Path* Dijkstra::makePath(int source, int target) {
	Path* path = new Path();
    if(previous[target]==UNDEFINED) {
        return NULL;
    }
	for(int p = target ; p!= source; p=previous[p]) {
		path->addVertex(p, 0);
	}
	path->addVertex(source,0);
	path->setWeight(dis[target]);
	return path->reverse();
}


Path* Dijkstra::getShortestPath(int source, int target) {

	for(int i=0;i<MAX_SIZE;i++) { 
        dis[i]=INF;     //Set initial distances to Infinity
        previous[i]=UNDEFINED;   //Set initial the initial previous vertex to UNDEFINED
        vis[i] = 0;
    }
    //Custom Comparator for Determining priority for priority queue (shortest edge comes first)
    class prioritize{public: bool operator ()(pair<int, int>&p1 ,pair<int, int>&p2){return p1.second>p2.second;}};
    priority_queue<pair<int,int> ,vector<pair<int,int> >, prioritize> pq; //Priority queue to store vertex,weight pairs
    pq.push(make_pair(source,dis[source]=0)); //Pushing the source with distance from itself as 0
    while(!pq.empty()) {
        pair<int, int> curr=pq.top(); //Current vertex. The shortest distance for this has been found
        int cv=curr.first,cw=curr.second; //'cw' the final shortest distance for this vertex
        if(cv == target) 
            return makePath(source, target);
        pq.pop();
        if(vis[cv]) //If the vertex is already visited, no point in exploring adjacent vertices
                continue;
            vis[cv]=true; 
            for(int i=0;i<graph->a[cv].size();i++) //Iterating through all adjacent vertices
                if(!vis[graph->a[cv][i].first] && graph->a[cv][i].second+cw<dis[graph->a[cv][i].first]) { //If this node is not visited and the current parent node distance+distance from there to this node is shorted than the initial distace set to this node, update it
                    pq.push(make_pair(graph->a[cv][i].first,(dis[graph->a[cv][i].first]=graph->a[cv][i].second+cw))); //Set the new distance and add to priority queue
                    previous[graph->a[cv][i].first] = cv;
                }
    }
    return makePath(source, target);
}



