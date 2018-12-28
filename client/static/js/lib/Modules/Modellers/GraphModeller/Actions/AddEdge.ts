/// <reference path='./index.ts'/>
/// <reference path='../../../index.ts'/>
/// <reference path='../../../../Systems/index.ts'/>

class AddEdge extends GraphModellerAction
{
    protected EdgeId: number | undefined;

    protected From          : State;
    protected To            : State;
    protected Transition    : Transition;

    public constructor(drawer : GraphDrawer, from : State | number, to : State | number, transition : Transition, id?: number)
    {
        super(drawer);

        let graph = Store.GetInstance().GetGraph();
        if(typeof from == "number") {
            from = graph.GetState(from);
        }
        if(typeof to == "number") {
            to = graph.GetState(to);
        }

        this.From       = from;
        this.To         = to;
        this.Transition = transition;
        this.EdgeId     = id;
    }

    public Invoke()
    {
        let graph = Store.GetInstance().GetGraph();

        let id = this.EdgeId == null ? GraphModellerAction.ElementCounter : this.EdgeId;

        let edge = new Edge(id, this.From, this.To, this.Transition);
        graph.AddEdge(edge);

        GraphModellerAction.ElementCounter++;

        this.EdgeId = id;

        let drawer = this.Drawer;

        let fd = drawer.GetStateDrawing(this.From.GetId());
        let td = drawer.GetStateDrawing(this.To.GetId());

        let d = this.From.equals(this.To) ?
            new SelfLoopDrawing(this.Drawer, edge, fd, this.Transition, 0) :
            new OtherEdgeDrawing(this.Drawer, edge, fd, td, this.Transition);
        drawer.AddEdgeDrawing(d);
    }

    public Undo()
    {
        let a = new RemoveEdge(this.Drawer, this.EdgeId)
        a.Invoke();
    }
}