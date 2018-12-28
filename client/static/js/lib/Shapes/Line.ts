/// <reference path='./TwoPointShape.ts'/>

class Line extends TwoPointShape
{
    public Curvature : number;

    public constructor(a : Point, b : Point)
    {
        super(a, b);

        this.Curvature = 0;
    }

    protected SetPath(ctx : CanvasRenderingContext2D)
    {
        let c = this.GetCurvePoint();

        ctx.bezierCurveTo(
            this.StartPoint.X, this.StartPoint.Y,
            c.X, c.Y,
            this.EndPoint.X, this.EndPoint.Y
        );
    }

    protected FillShape(ctx : CanvasRenderingContext2D)
    {
        this.SetPath(ctx);
        ctx.fill();
    }

    protected StrokeShape(ctx : CanvasRenderingContext2D)
    {
        this.SetPath(ctx);
        ctx.stroke();
    }

    public Hit(context : CanvasRenderingContext2D, point : Point, strokeWidth = 15)
    {
        context.beginPath();
        this.SetPath(context);
        
        context.lineWidth = strokeWidth;
        let res = context.isPointInStroke(point.X, point.Y);
        context.closePath();
        return res;
    }
    
    public GetMidPoint()
    {
        let start = this.StartPoint;
        let end   = this.EndPoint;
        
        let dx = end.X - start.X;
        let dy = end.Y - start.Y;

        let mx = start.X + (dx / 2);
        let my = start.Y + (dy / 2);

        let m = new Point(mx, my);
        return m;
    }

    public GetCurvePoint()
    {
        let m = this.GetMidPoint();
        let angle = Math.calcAngle(this.StartPoint, this.EndPoint);
        let newAngle = (-angle + (0.5 * Math.PI)) % (2 * Math.PI);

        let px = m.X + this.Curvature * Math.cos(newAngle);
        let py = m.Y + this.Curvature * Math.sin(newAngle);

        let p = new Point(px, py);
        return p;
    }

    public GetCurveCutPoint()
    {
        let m = this.GetMidPoint();
        let angle = Math.calcAngle(this.StartPoint, this.EndPoint);
        let newAngle = (-angle + (0.5 * Math.PI)) % (2 * Math.PI);

        let px = m.X + (this.Curvature / ((3/4) * Math.PI)) * Math.cos(newAngle);
        let py = m.Y + (this.Curvature / ((3/4) * Math.PI)) * Math.sin(newAngle);

        let p = new Point(px, py);
        return p;
    }
}