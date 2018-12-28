/// <reference path='../Utils/Extensions/Math.ts'/>

/// <reference path='../Utils/Datastructures/Size/Size.ts'/>
/// <reference path='../Utils/Datastructures/Point/Point.ts'/>

abstract class Shape
{
    public Fill(context : CanvasRenderingContext2D) : void
    {
        context.save();
        context.beginPath();
        this.FillShape(context);
        context.closePath();
        context.restore();
    }
    
    public Stroke(context : CanvasRenderingContext2D) : void
    {
        context.save();
        context.beginPath();
        this.StrokeShape(context);
        context.closePath();
        context.restore();
    }

    public abstract Hit (context : CanvasRenderingContext2D , point : Point) : boolean;

    protected abstract SetPath(context : CanvasRenderingContext2D) : void;    
    protected abstract StrokeShape(context : CanvasRenderingContext2D) : void;
    protected abstract FillShape(context : CanvasRenderingContext2D) : void;
}