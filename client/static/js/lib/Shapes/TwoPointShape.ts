/// <reference path='./OnePointShape.ts'/>

abstract class TwoPointShape extends OnePointShape
{
    EndPoint : Point;
    public constructor(a : Point, b : Point)
    {
        super(a);
        this.EndPoint = b;
    }

    public GetSize()
    {
        let s = new Size(
            Math.CalcHorizontalDistance(this.StartPoint, this.EndPoint),
            Math.CalcVerticalDistance(this.StartPoint, this.EndPoint)
        );
        return s;
    }
    
    public GetMidPoint()
    {
        let tl = new Point(
            Math.min(this.StartPoint.X, this.EndPoint.X), 
            Math.min(this.StartPoint.Y, this.EndPoint.Y)
        );
        let br = new Point(
            Math.max(this.StartPoint.X, this.EndPoint.X),
            Math.max(this.StartPoint.Y, this.EndPoint.Y)
        );

        let dx = Math.CalcHorizontalDistance(tl, br);
        let dy = Math.CalcVerticalDistance(tl, br);

        let mid = new Point(
            tl.X + dx / 2,
            tl.Y + dy / 2
        );
        return mid;
    }
}