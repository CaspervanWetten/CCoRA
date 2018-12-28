/// <reference path='./index.ts'/>

class SelfLoopDrawing extends EdgeDrawing implements IMoveableDrawing
{
    protected Direction : number;

    public constructor(drawer : GraphDrawer, edge : Edge, from : StateDrawing, trans : Transition, dir = 0.25)
    {
        super(drawer, edge, from, trans);
        this.Direction = dir;
    }

    public MoveTo(position : Point)
    {
        let t = Math.calcAngle(this.GetFromDrawing().GetPosition(), position);
        this.SetDirection(t);
    }

    public GetShape(context: CanvasRenderingContext2D): Shape {
        let drawing = this.From;
        let pos = drawing.GetPosition();
        let r   = Settings.GetInstance().GetEdgeRadius();

        let bt = this.From.GetShape(context).GetBoundingPoint(this.Direction);
        let center = bt;

        let shape = new Circle(
            center,
            r,
            0,
            Math.PI * 2
        );
        return shape;
    }

    protected DrawShape(context: CanvasRenderingContext2D) {
        let shape = this.GetShape(context);
        shape.Stroke(context);
        this.DrawText(context);
    }
    
    protected DrawText(context: CanvasRenderingContext2D) {
        // this.SetTextStyle(context);        
        context.save()
        StyleManager.SetStandardStateStyle(context);
        let shape = this.From.GetShape(context);
        context.restore();
        let r = Settings.GetInstance().GetEdgeRadius();
        let p = this.From.GetPosition()
        let point  = shape.GetBoundingPoint(this.Direction);
        point.X += r * Math.cos(this.Direction);
        point.Y -= r * Math.sin(this.Direction);
        // get font height
        context.save();
        StyleManager.SetTextStyleSelfLoop(context);
        let reg = context.font.match(/^[0-9]+/i);
        let textHeight = 0;
        let heightPadding = 10;
        if(reg) {
            textHeight = Number(reg[0]) + heightPadding;
        }
        let textWidth = context.measureText(this.Edge.GetTransition()).width;
        context.beginPath();
        context.clearRect(point.X - textWidth / 2, point.Y - textHeight / 2, textWidth, textHeight);        
        context.fillText(this.Edge.GetTransition(), point.X, point.Y);
        context.closePath();
        context.restore();
    }

    public SetDirection(dir : number)
    {
        this.Direction = dir % (Math.PI * 2);
    }

    public GetPosition()
    {
        return this.From.GetPosition();
    }
}