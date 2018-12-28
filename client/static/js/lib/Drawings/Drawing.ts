/// <reference path='../Shapes/index.ts'/>

abstract class Drawing
{
    public Draw(context : CanvasRenderingContext2D)
    {
        context.save();
        this.DrawShape(context);
        context.restore();
    }
    
    public Hit(context : CanvasRenderingContext2D, point : Point)
    {
        context.save();
        let shape = this.GetShape(context);
        let res = shape.Hit(context, point);
        context.restore();
        return res;
    }

    public abstract GetShape(context : CanvasRenderingContext2D) : Shape;

    protected abstract DrawShape(context : CanvasRenderingContext2D);
}