/// <reference path='./index.ts'/>

class Circle extends OnePointShape
{
    protected StartRadian : number;
    protected EndRadian   : number;
    protected Radius      : number;

    public constructor(position : Point, radius : number, start : number, end : number)
    {
        super(position);
        this.Radius = radius;
        this.StartRadian = start;
        this.EndRadian   = end;
    }

    protected SetPath(context: CanvasRenderingContext2D): void {
        context.arc(
            this.StartPoint.X,
            this.StartPoint.Y,
            this.Radius,
            this.StartRadian,
            this.EndRadian
        );
    }
    
    protected StrokeShape(context : CanvasRenderingContext2D)
    {
        this.SetPath(context);
        context.stroke();
    }

    protected FillShape(context : CanvasRenderingContext2D)
    {
        this.SetPath(context);
        context.fill();
    }

    public Hit(context : CanvasRenderingContext2D, position : Point)
    {
        let padding = 10;

        let dx = Math.CalcHorizontalDistance(position, this.StartPoint);
        let dy = Math.CalcVerticalDistance(position, this.StartPoint);
        return Math.sqrt( dx * dx + dy * dy ) <= this.Radius + padding;
    }
}