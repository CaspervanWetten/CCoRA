/// <reference path='./Edge.ts'/>
/// <reference path='../State.ts'/>

/// <reference path='../../../vendor/Definitions/Hashtable.d.ts'/>
/// <reference path='../../../vendor/Definitions/Hashset.d.ts'/>
class Graph
{
    protected States        : IHashtable<number, State>;
    protected Edges         : IHashtable<number, Edge>;
    protected Initial       : State | undefined;

    public constructor()
    {
        this.States     = new Hashtable();
        this.Edges      = new Hashtable();
        this.Initial    = undefined;
    }

    public AddState(state : State)
    {
        let id = state.GetId();
        this.States.put(id, state);
    }

    public RemoveState(state : State)
    {
        if(this.States.containsKey(state.GetId()))
        {
            let edges = this.Edges.values();
            for(let i = 0; i < edges.length; i++)
            {
                let edge = edges[i];
                if(edge.GetFromState().equals(state) || edge.GetToState().equals(state)) {
                    let id = edge.GetId();
                    this.Edges.remove(id);
                }
            }
            this.States.remove(state.GetId());
        }
    }
    
    public ReplaceState(old : State | number, _new : State)
    {
        if(old instanceof State) {
            old = old.GetId();
        }

        if(this.States.containsKey(old))
        {
            let oldState = this.States.get(old);
            let edges = this.Edges.values();
            for(let i = 0; i < edges.length; i++)
            {
                let e = edges[i];
                if(e.GetFromState().equals(oldState)) {
                    e.SetFromState(_new);
                }
                if(e.GetToState().equals(oldState)) {
                    e.SetToState(_new)
                }
            }
            let id = old;
            _new.SetId(id);
            this.States.put(id, _new);
        }
    }

    public ContainsState(state : State | number)
    {
        let id = state;
        if(state instanceof State) {
            id = state.GetId();
        }
        return this.States.containsKey(<number>id);
    }

    // all edges pointing to the state
    public GetFromNeighbours(state : State | number)
    {
        if(typeof state == "number") {
            state = this.States.get(state);
        }

        let result : Edge[] = [];
        let e = this.Edges.values();
        for(let i = 0; i < e.length; i++) {
            if(e[i].GetFromState().equals(state)) {
                result.push(e[i]);
            }
        }
        return result;
    }

    // edges leaving the state
    public GetToNeighbours(state : State | number)
    {
        if(typeof state == "number") {
            state = this.States.get(state);
        }

        let result : Edge[] = [];
        let e = this.Edges.values();
        for(let i = 0; i < e.length; i++) {
            if(e[i].GetToState().equals(state) && !e[i].GetFromState().equals(state)) {
                result.push(e[i]);
            }
        }
        return result;
    }

    public GetNeighbours(state : State | number)
    {
        if(typeof state == "number") {
            state = this.States.get(state);
        }

        let result = [];
        let e = this.Edges.values();
        for(let i = 0; i < e.length; i++)
        {
            if(e[i].GetFromState().equals(state) || e[i].GetToState().equals(state)) {
                result.push(e[i]);
            }
        }

        return result;
    }

    public AddEdge(edge : Edge)
    {
        let id = edge.GetId();
        this.Edges.put(id, edge);
    }

    public RemoveEdge(edge : Edge | number)
    {
        let id = typeof edge == "number" ? edge : edge.GetId();
        this.Edges.remove(id);
    }

    public ContainsEdge(edge : Edge | number)
    {
        let id = edge;
        if (edge instanceof Edge) {
            id = edge.GetId();
        }
        return this.Edges.containsKey(<number>id);
    }

    public IsEmpty()
    {
        return this.States.isEmpty();
    }

    //#region Getters and Setters
    public GetInitialState()
    {
        return this.Initial;
    }
    
    public SetInitialState(state : State | number | undefined) {
        if(state == null) {
            this.Initial = undefined;
            return;
        }
        let s = state;
        if(typeof state == "number") {
            s = this.States.get(state);
        }
        this.Initial = <State>s;
    }

    public GetStates()
    {
        return this.States;
    }

    public GetEdges()
    {
        return this.Edges;
    }

    public GetState(id : number)
    {
        if(this.States.containsKey(id))
        {
            return this.States.get(id);
        }
    }

    public GetEdge(id : number)
    {
        if(this.Edges.containsKey(id)) {
            return this.Edges.get(id);
        }
    }
    //#endregion

    public toJSON(param) {
	let converter = new GraphToJson(this);
	return converter.Convert();
    }
}
