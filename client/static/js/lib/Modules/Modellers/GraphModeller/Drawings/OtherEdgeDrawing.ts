/// <reference path='./index.ts'/>
/// <reference path='../../../../Shapes/index.ts'/>
/// <reference path='../../../../Systems/index.ts'/>

/// <reference path='../../../../Utils/Datastructures/Point/Point.ts'/>

class OtherEdgeDrawing extends EdgeDrawing
{
    protected To            : StateDrawing;
    protected Curvature     : number = 0;

    public constructor(drawer : GraphDrawer, edge : Edge, from : StateDrawing, to : StateDrawing, transition : string)
    {
        super(drawer, edge, from, transition);
        this.To         = to;
        this.Curvature  = 0;
    }

    public GetShape(context : CanvasRenderingContext2D) : Arrow
    {
        context.save();
        StyleManager.SetStandardStateStyle(context);
        let fromShape = this.From.GetShape(context);
        let toShape   = this.To.GetShape(context);
        context.restore();

        let fromPos   = fromShape.StartPoint;
        let fromSize  = fromShape.GetSize();
        let toPos     = toShape.StartPoint;
        let toSize    = toShape.GetSize();

        let angleTo   = Math.calcAngle(toPos, fromPos);
        let angleFrom = Math.calcAngle(fromPos, toPos);

        let bt = fromShape.GetBoundingPoint(angleFrom);
        let bf = toShape.GetBoundingPoint(angleTo);
     
        let shape = new Arrow(bt, bf);
        shape.TipHeight = 18;
        shape.TipWidth  = 10;
        shape.Curvature = this.Curvature;
        
        return shape;
    }

    public DrawShape(context : CanvasRenderingContext2D)
    {
        let shape = this.GetShape(context);
        shape.Fill(context);
        this.DrawText(context);
    }

    protected DrawText(context : CanvasRenderingContext2D)
    {
        let shape = this.GetShape(context);
        let s = this.From.GetSize(context);
        let p = this.From.GetPosition();
        
        let point = shape.GetCurveCutPoint();
        context.save();
        StyleManager.SetTextStyleEdge(context);
        // get font height
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
    //#region Getters and Setters
    public GetToDrawing() {
        return this.To;
    }

    public GetCurvature() {
        return this.Curvature;
    }

    public SetCurvature(curv : number) {
        this.Curvature = curv;
    }
    //#endregion
}