/// <reference path='./Line.ts'/>
/// <reference path='./Triangle.ts'/>

class Arrow extends Line
{
    TipWidth : number;
    TipHeight : number;

    public constructor(a : Point, b : Point)
    {
        super(a, b);

        this.TipWidth = 15;
        this.TipHeight = 15;
    }

    protected StrokeShape(context : CanvasRenderingContext2D)
    {
        super.StrokeShape(context);
        let tip = this.GetArrowTip();
        tip.Stroke(context);
    }

    protected FillShape(context : CanvasRenderingContext2D)
    {
        super.StrokeShape(context);
        let tip = this.GetArrowTip();
        tip.Fill(context);
        tip.Stroke(context);        
    }

    protected GetArrowTip() : Triangle
    {
        let width = this.TipWidth;
        let height = this.TipHeight;
        let angle = Math.calcAngle(this.GetCurvePoint(), this.EndPoint);

        let mx = this.EndPoint.X - height * Math.cos(angle);
        let my = this.EndPoint.Y + height * Math.sin(angle);

        let w = width / 2;

        let theta = (angle + 0.5 * Math.PI) % (2 * Math.PI);
        let ax = mx + w * Math.cos(theta);
        let ay = my - w * Math.sin(theta);

        let bx = mx - w * Math.cos(theta);
        let by = my + w * Math.sin(theta);

        return new Triangle(new Point(ax, ay), new Point(bx, by), this.EndPoint);
    }
}