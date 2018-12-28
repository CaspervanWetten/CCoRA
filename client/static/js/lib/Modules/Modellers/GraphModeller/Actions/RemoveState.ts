/// <reference path='./GraphModellerAction.ts'/>

class RemoveState extends GraphModellerAction
{
    public StateId : number;

    protected StateCopy : State | undefined;
    protected Position  : Point | undefined;
    
    protected FromNeighbours    : Edge[];
    protected ToNeighbours      : Edge[];

    protected InitialState      : boolean;

    public constructor(modeller : GraphDrawer, id : number)
    {
        super(modeller);
        this.StateId = id;

        this.StateCopy = undefined;
        this.Position  = undefined;
        this.FromNeighbours = [];
        this.ToNeighbours   = [];

        this.InitialState = false;
    }

    public Undo() {
        let a = new AddState (
            this.Drawer,
            this.StateCopy.ToSystemString(),
            this.Position,
            this.StateId,
        );
        a.Invoke();

        if(this.InitialState) {
            let graph = Store.GetInstance().GetGraph();
            graph.SetInitialState(this.StateId);
        }

        for(let i = 0; i < this.FromNeighbours.length; i++) {
            let e = new AddEdge(
                this.Drawer,
                this.StateCopy,                
                this.FromNeighbours[i].GetToState(),
                this.FromNeighbours[i].GetTransition(),
                this.FromNeighbours[i].GetId());
            e.Invoke();
        }

        for(let i = 0; i < this.ToNeighbours.length; i++) {
            let e = new AddEdge(
                this.Drawer, 
                this.ToNeighbours[i].GetFromState(), 
                this.StateCopy,
                this.ToNeighbours[i].GetTransition(), 
                this.ToNeighbours[i].GetId());
            e.Invoke();
        }
    }
    public Invoke() {
        let drawer  = this.Drawer;
        let graph   = Store.GetInstance().GetGraph();
        let state   = graph.GetStates().get(this.StateId);

        let initial = graph.GetInitialState();//.GetId();
        if(initial && initial.GetId() == this.StateId) {
            this.InitialState = true;
            graph.SetInitialState(undefined);
        }

        let drawing = drawer.GetStateDrawing(state.GetId());
        
        this.StateCopy = state;
        this.Position  = drawing.GetPosition();

        this.FromNeighbours = graph.GetFromNeighbours(state);
        this.ToNeighbours   = graph.GetToNeighbours(state);

        graph.RemoveState(state);
        drawer.RemoveStateDrawing(this.StateId);
    }
    
}