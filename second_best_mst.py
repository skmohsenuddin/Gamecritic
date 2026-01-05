import sys
from collections import deque

def main():
    data = sys.stdin.read().split()
    if not data:
        print(-1)
        return
    
    n = int(data[0])
    m = int(data[1])
    
    edges = []
    for i in range(m):
        u = int(data[2 + i*3]) - 1
        v = int(data[3 + i*3]) - 1
        w = int(data[4 + i*3])
        edges.append((w, u, v))
    
    edges.sort()
    
    parent = list(range(n))
    rank = [0] * n
    
    def find(x):
        if parent[x] != x:
            parent[x] = find(parent[x])
        return parent[x]
    
    def union(x, y):
        px, py = find(x), find(y)
        if px == py:
            return False
        if rank[px] < rank[py]:
            px, py = py, px
        parent[py] = px
        if rank[px] == rank[py]:
            rank[px] += 1
        return True
    
    mst_edges = []
    mst_weight = 0
    mst_edge_set = set()
    
    parent = list(range(n))
    rank = [0] * n
    
    for w, u, v in edges:
        if union(u, v):
            mst_edges.append((u, v, w))
            mst_edge_set.add((u, v))
            mst_edge_set.add((v, u))
            mst_weight += w
            if len(mst_edges) == n - 1:
                break
    
    if len(mst_edges) < n - 1:
        print(-1)
        return
    
    mst_graph = [[] for _ in range(n)]
    for u, v, w in mst_edges:
        mst_graph[u].append((v, w))
        mst_graph[v].append((u, w))
    
    def find_max_edge_on_path(start, end):
        visited = [False] * n
        prev = [-1] * n
        edge_weight_to = [0] * n
        
        queue = deque([start])
        visited[start] = True
        
        while queue:
            node = queue.popleft()
            if node == end:
                break
            
            for neighbor, weight in mst_graph[node]:
                if not visited[neighbor]:
                    visited[neighbor] = True
                    prev[neighbor] = node
                    edge_weight_to[neighbor] = weight
                    queue.append(neighbor)
        
        if not visited[end]:
            return None
        
        max_weight = 0
        current = end
        while current != start:
            max_weight = max(max_weight, edge_weight_to[current])
            current = prev[current]
        
        return max_weight
    
    second_best_weight = float('inf')
    
    for w, u, v in edges:
        if (u, v) in mst_edge_set:
            continue
        
        max_edge_in_cycle = find_max_edge_on_path(u, v)
        
        if max_edge_in_cycle is not None:
            candidate_weight = mst_weight - max_edge_in_cycle + w
            
            if candidate_weight > mst_weight:
                second_best_weight = min(second_best_weight, candidate_weight)
    
    if second_best_weight == float('inf'):
        print(-1)
    else:
        print(second_best_weight)

if __name__ == '__main__':
    main()

