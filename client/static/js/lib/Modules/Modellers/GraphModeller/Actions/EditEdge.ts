/// <reference path='./GraphModellerAction.ts'/>

class EditEdge extends GraphModellerAction
{
    protected EdgeId : number;
    protected OldTransition : Transition;
    protected NewTransition : Transition;

    public constructor(drawer : GraphDrawer, edge : Edge | number, _new : Transition)
    {
        super(drawer);

        let graph = Store.GetInstance().GetGraph();
        if (!(edge instanceof Edge)) {
            edge = graph.GetEdge(edge);
        }
        
        this.EdgeId = edge.GetId();
        this.OldTransition = edge.GetTransition();
        this.NewTransition = _new;
    }

    public Invoke()
    {
        let graph = Store.GetInstance().GetGraph();
        let edge = graph.GetEdge(this.EdgeId);
        let drawing = this.Drawer.GetEdgeDrawing(this.EdgeId);
        
        edge.SetTransition(this.NewTransition);
    }

    public Undo()
    {
        let graph = Store.GetInstance().GetGraph();
        let edge = graph.GetEdge(this.EdgeId);

        edge.SetTransition(this.OldTransition);
    }
}