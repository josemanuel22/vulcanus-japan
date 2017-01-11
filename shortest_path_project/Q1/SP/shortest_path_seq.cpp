
//Implementation for Dijkstra's SSSP(Single source shortest path) algorithm
//This is an optimized algorithm running in O(E*log(V))
#include <iostream>
#include <fstream>
#include <queue>
#include <vector>
#include <climits>
using namespace std;
#define INF INT_MAX //Infinity
#define UNDEFINED -1
const int sz=10001; //Maximum possible number of vertices. Preallocating space for DataStructures accordingly
vector<pair<int,int> > a[sz]; //Adjacency list
int dis[sz]; //Stores shortest distance
bool vis[sz]={0}; //Determines whether the node has been visited or not
int previous[sz]; //previous vertex in the shortest path  leading to it.

void Dijkstra(int source, int target, int n) { //Algorithm for SSSP 

    for(int i=0;i<sz;i++) { 
        dis[i]=INF;     //Set initial distances to Infinity
        previous[i]=UNDEFINED;   //Set initial the initial previous vertex to UNDEFINED
    }
    //Custom Comparator for Determining priority for priority queue (shortest edge comes first)
    class prioritize{public: bool operator ()(pair<int, int>&p1 ,pair<int, int>&p2){return p1.second>p2.second;}};
    priority_queue<pair<int,int> ,vector<pair<int,int> >, prioritize> pq; //Priority queue to store vertex,weight pairs
    pq.push(make_pair(source,dis[source]=0)); //Pushing the source with distance from itself as 0
    while(!pq.empty()) {
        pair<int, int> curr=pq.top(); //Current vertex. The shortest distance for this has been found
        int cv=curr.first,cw=curr.second; //'cw' the final shortest distance for this vertex
        if(cv == target) return;
        pq.pop();
        if(vis[cv]) //If the vertex is already visited, no point in exploring adjacent vertices
                continue;
            vis[cv]=true; 
            for(int i=0;i<a[cv].size();i++) //Iterating through all adjacent vertices
                if(!vis[a[cv][i].first] && a[cv][i].second+cw<dis[a[cv][i].first]) { //If this node is not visited and the current parent node distance+distance from there to this node is shorted than the initial distace set to this node, update it
                    pq.push(make_pair(a[cv][i].first,(dis[a[cv][i].first]=a[cv][i].second+cw))); //Set the new distance and add to priority queue
                    previous[a[cv][i].first] = cv;
                }
        }
}   

int main(int argc, char* argv[]) {  //Driver Function for Dijkstra SSSP

    ifstream file;

    int n,m;        //Number of vertices and edges
    int nr;         //Number of resources
    int lr, ur;     //lower limit & upper limit on the resources consumed on the chosen path
    int x,y,w,c;

    if(argc < 2) {
        cout<<"Il faut indiquer le nom la route du fichier contenant le graphe Ex: ./shortest_path_seq ./Tests/rcsp1.txt"<<endl;
        return 0;
    }

    file.open(argv[1]); //On ouvre le fichier contenant le graphe

    file>>n>>m>>nr;
    file>>lr>>ur;
    //cout<<n<<" "<<m<<" "<<nr<<" "<<lr<<" "<<ur<<endl;
    int aux;
    for(int i=0;i<n; i++) {
        file>>aux;
    } 
    for(int i=0;i<m;i++) { //Building Graph
        file>>x>>y>>w>>c; //Vertex1, Vertex2, weight of edge
        //cout<<x<<" "<<y<<" "<<w<<" "<<c<<endl;
        a[x].push_back(make_pair(y,w));
        //a[y].push_back(make_pair(x,w));
    }
    //cout<<"Enter source for Dijkstra's SSSP algorithm\n";
    int source,target;
    cout<<"Introduire le noeud source: ";
    cin>>source;
    cout<<"Introduire le noeud final: ";
    cin>>target;
    Dijkstra(source, target, n);//SSSP from source (Also passing number of vertices as parameter)
    cout<<"Source is: "<<source<<endl;
    cout<<"Target is: "<<target<<endl;
    int p = target;
    if(previous[p] == UNDEFINED) {
        cout<<"Target unreachable from the source";
    }
    cout<<target<<"<-";
    while((p=previous[p]) != source)
        cout<<p<<"<-";
    cout<<source<<endl;
    return 0;
}