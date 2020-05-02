/// <reference path='./index.ts'/>

/// <reference path='../../Systems/Graph/index.ts'/>

class GraphToJson implements IConverter
{
    public Graph : Graph;

    public constructor(graph : Graph)
    {
        this.Graph = graph;
    }

    public Convert()
    {
        let graph = this.Graph;
        let states = graph.GetStates().values();
        let edges  = graph.GetEdges().values();
        let initial = graph.GetInitialState();

        let s = [];
        for(let i = 0; i < states.length; i++) {
            let state = states[i];
            let ss = state.ToSystemString();
            let sid = state.GetId();
            let j = new JSONState(ss, sid);
            s.push(j);
        }

        let e = [];
        for(let i = 0; i < edges.length; i++) {
            let edge = edges[i];
            let id   = edge.GetId();
            let fid  = edge.GetFromState().GetId();
            let tid  = edge.GetToState().GetId();
            let j = new JSONEdge(id, fid, tid, edge.GetTransition());
            e.push(j);
        }

        let inij = undefined;

        if(initial != null) {
            let inid = initial.GetId();
            inij = new JSONInitialState(inid);
        }   
        let obj = {};
        obj["states"] = s;
        obj["edges"] = e;
        obj["initial"] = inij;
        
	return obj;
    }
}

class JSONState
{
    public state : string;
    public id    : number;

    public constructor(s : string, id : number)
    {
        this.state = s;
        this.id = id;
    }
}

class JSONEdge
{
    public id     : number;
    public fromId : number;
    public toId   : number;
    public transition : string;

    public constructor(id : number, fid : number, tid : number, t : string)
    {
        this.id     = id;
        this.fromId = fid;
        this.toId   = tid;
        this.transition = t;
    }
}

class JSONInitialState
{
    public id : number;

    public constructor(id : number)
    {
        this.id = id;
    }
}
