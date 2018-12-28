/// <reference path='./index.ts'/>
class EditState extends StateAction
{
    protected OldState : State;
    protected NewState : State;

    public constructor(drawer : GraphDrawer, old : State, _new : State | string)
    {
        super(drawer);
        this.OldState = old;
        if(typeof _new == "string") {
            _new = this.ParseStateString(_new, old.GetId());
        }
        this.NewState = _new;
    }

    public Invoke()
    {
        let drawer = this.Drawer;
        let graph = Store.GetInstance().GetGraph();
        graph.ReplaceState(this.OldState, this.NewState);

        let drawing = drawer.GetStateDrawing(this.OldState.GetId());
        drawing.SetState(this.NewState);
    }

    public Undo()
    {
        let drawer = this.Drawer;
        let graph = Store.GetInstance().GetGraph();
        graph.ReplaceState(this.NewState, this.OldState);

        let drawing = drawer.GetStateDrawing(this.NewState.GetId());
        drawing.SetState(this.OldState);
    }
}