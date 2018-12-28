/// <reference path='./index.ts'/>

class AddState extends StateAction
{  
    protected StateString : string;
    protected Position    : Point | undefined;
    protected AssignedId  : number | undefined;

    public constructor(drawer : GraphDrawer, s : string, position? : Point, id?: number)
    {
        super(drawer);
        this.StateString = s;
        this.AssignedId = id;
        this.Position = position;
    }

    public Invoke() {
        let state = this.ParseStateString(this.StateString, this.AssignedId);
        let graph = Store.GetInstance().GetGraph();
        graph.AddState(state);
        let drawer = this.Drawer;

        let p = this.Position != null? this.Position : new Point(0,0);
        let drawing = new StateDrawing(this.Drawer, state, p);
        drawer.AddStateDrawing(drawing);

        this.AssignedId = state.GetId();
    }

    public Undo() {
        let drawer = this.Drawer;
        this.Position = drawer.GetStateDrawing(this.AssignedId).GetPosition();
        
        let action = new RemoveState(this.Drawer, this.AssignedId);
        action.Invoke();
    }
}