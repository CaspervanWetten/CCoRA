/// <reference path='./index.ts'/>

class RemoveEdge extends GraphModellerAction
{
    protected EdgeId   : number;

    protected EdgeCopy : Edge;

    public constructor(drawer : GraphDrawer, edgeId: number)
    {
        super(drawer);
        this.EdgeId = edgeId;
    }

    public Invoke()
    {
        let drawer = this.Drawer;
        let graph  = Store.GetInstance().GetGraph();
        let edge   = graph.GetEdge(this.EdgeId);

        this.EdgeCopy = edge;
        let drawing = this.Drawer.GetEdgeDrawing(this.EdgeId);

        graph.RemoveEdge(this.EdgeId);
        drawer.RemoveEdgeDrawing(this.EdgeId);
    }

    public Undo()
    {
        let a = new AddEdge(
            this.Drawer,
            this.EdgeCopy.GetFromState(),
            this.EdgeCopy.GetToState(),
            this.EdgeCopy.GetTransition(),
            this.EdgeId
        );
        a.Invoke();
    }
}