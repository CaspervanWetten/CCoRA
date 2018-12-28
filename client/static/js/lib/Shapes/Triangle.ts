/// <reference path='./ThreePointShape.ts'/>

class Triangle extends ThreePointShape
{
    public constructor(a : Point, b : Point, c : Point)
    {
        super(a, b, c);
    }
    
    protected FillShape(context : CanvasRenderingContext2D)
    {
        this.SetPath(context);
        context.fill();
    }

    protected StrokeShape(context : CanvasRenderingContext2D)
    {
        this.SetPath(context);
        context.stroke();
    }

    protected SetPath(ctx : CanvasRenderingContext2D)
    {
        ctx.beginPath();
        ctx.moveTo(this.StartPoint.X, this.StartPoint.Y);
        ctx.lineTo(this.ThirdPoint.X, this.ThirdPoint.Y);
        ctx.lineTo(this.EndPoint.X, this.EndPoint.Y);
        ctx.lineTo(this.StartPoint.X, this.StartPoint.Y);
        ctx.closePath();
    }

    public Hit(context : CanvasRenderingContext2D, p : Point)
    {
        return false;
    }
}