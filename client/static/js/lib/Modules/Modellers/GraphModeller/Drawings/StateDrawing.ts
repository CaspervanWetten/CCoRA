/// <reference path='./index.ts'/>
/// <reference path='../StyleManager/index.ts'/>

class StateDrawing extends GraphElementDrawing implements IMoveableDrawing
{
    protected State    : State;    
    protected Position : Point; // topleft

    public constructor(drawer : GraphDrawer, state : State, position = new Point(0, 0))
    {
        super(drawer);

        this.State    = state;
        this.Position = position;
    }

    public GetShape(context : CanvasRenderingContext2D) : Box
    {
        let padding = Settings.GetInstance().GetStatePadding();

        context.save();
        StyleManager.SetTextStyleState(context);
        let width = context.measureText(this.State.ToDisplayString()).width + padding;
        context.restore();

        let height = Settings.GetInstance().GetStateHeight();

        let tl = new Point(this.Position.X, this.Position.Y);
        let br = new Point(this.Position.X + width, this.Position.Y + height);
        
        let box = new Box(tl, br);
        return box;
    }

    public DrawShape(context : CanvasRenderingContext2D)
    {
        let shape = this.GetShape(context);
        shape.Fill(context);
        shape.Stroke(context);
        this.DrawText(context);
    }

    protected DrawText(context : CanvasRenderingContext2D)
    {
        let s       = this.GetShape(context).GetSize();
        let text    = this.State.ToDisplayString();

        let wh = s.Width  / 2;
        let hh = s.Height / 2;

        context.save();
        StyleManager.SetTextStyleState(context);
        context.fillText(text, this.Position.X + wh, this.Position.Y + hh);
        context.restore();
    }

    public MoveTo(position : Point)
    {
        this.Position = position;
    }

    //#region Getters and Setters
    public GetState()
    {
        return this.State;
    }

    public SetState(state : State)
    {
        this.State = state;
    }

    public GetPosition()
    {
        return this.Position;
    }

    public GetSize(context : CanvasRenderingContext2D)
    {
        let shape = this.GetShape(context)
        return shape.GetSize();
    }
    //#endregion
}