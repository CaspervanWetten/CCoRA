/// <reference path='./TwoPointShape.ts'/>

class Box extends TwoPointShape
{
    public constructor(a : Point, b : Point)
    {
        let tl = new Point(
            Math.min(a.X, b.X),
            Math.min(a.Y, b.Y)
        );
        let br = new Point(
            Math.max(a.X, b.X),
            Math.max(a.Y, b.Y)
        );
        super(tl, br);
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
        ctx.rect(
            this.StartPoint.X,
            this.StartPoint.Y,
            Math.CalcHorizontalDistance(this.StartPoint, this.EndPoint),
            Math.CalcVerticalDistance(this.StartPoint, this.EndPoint)
        );
    }

    public Hit(context : CanvasRenderingContext2D, p : Point)
    {
        return p.X >= this.StartPoint.X &&
         p.X <= this.EndPoint.X && 
         p.Y >= this.StartPoint.Y &&
         p.Y <= this.EndPoint.Y;
    }
    
    public GetBoundingPoint(angle : number)
    {
        let size = this.GetSize();
        let midPoint = new Point(
            this.StartPoint.X + (size.Width / 2), 
            this.StartPoint.Y + (size.Height / 2)
        );

        let trAngle = Math.calcAngle(midPoint, new Point(this.EndPoint.X, this.StartPoint.Y));
        let tlAngle = Math.calcAngle(midPoint, this.StartPoint);
        let blAngle = Math.calcAngle(midPoint, new Point(this.StartPoint.X, this.EndPoint.Y));
        let brAngle = Math.calcAngle(midPoint, this.EndPoint);
        
        let qx, qy = 0;

        // angle is on left or right side
        if (
            angle < trAngle || angle > brAngle ||
            angle > tlAngle && angle < blAngle    
        )
        {
            let knownSide = size.Width / 2;
            let v = knownSide * Math.tan(angle);
            
            if(angle > tlAngle && angle <= blAngle) {
                knownSide *= -1;
                v *= -1;
            }

            qx = midPoint.X + knownSide;
            qy = midPoint.Y - v;
        }
        // angle is on top or bottom side.
        else
        {
            let knownSide = size.Height / 2;
            let v = knownSide * (1 / Math.tan(angle));

            if(angle > blAngle && angle < brAngle) {
                knownSide *= -1;
                v *= -1;
            }
            qx = midPoint.X + v;
            qy = midPoint.Y - knownSide;
        }

        return new Point(qx, qy);
    }

    public GetAngles()
    {
        let result = [
            this.GetTopRightAngle(),
            this.GetTopLeftAngle(),
            this.GetBottomLeftAngle(),
            this.GetBottomRightAngle()
        ];
        return result;
    }

    public GetTopRightAngle()
    {
        let s = this.GetSize();
        let w = s.Width / 2;
        let h = s.Height / 2;
        return Math.atan(h / w);
    }

    public GetTopLeftAngle()
    {
        let a = this.GetTopRightAngle();
        a = Math.PI - a;
        return a;
    }

    public GetBottomLeftAngle()
    {
        let a = this.GetTopRightAngle();
        a += Math.PI;
        return a;
    }

    public GetBottomRightAngle()
    {
        let a = this.GetTopRightAngle();
        a = (2 * Math.PI) - a;
        return a;
    }
}

enum BoxSide {
    top,
    right,
    bottom,
    left
}