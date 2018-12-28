/// <reference path='./index.ts'/>

class SetInitialState extends StateAction
{
    protected StateId    : number | undefined;
    protected OldInitial : number | undefined;

    public constructor(drawer : GraphDrawer, id : number | undefined)
    {
        super(drawer);
        this.StateId = id;
    }

    public Invoke()
    {
        let graph = Store.GetInstance().GetGraph();
        this.OldInitial = graph.GetInitialState() ? graph.GetInitialState().GetId() : undefined;
        graph.SetInitialState(this.StateId);
    }

    public Undo()
    {
        let graph = Store.GetInstance().GetGraph();
        graph.SetInitialState(this.OldInitial);
    }
}