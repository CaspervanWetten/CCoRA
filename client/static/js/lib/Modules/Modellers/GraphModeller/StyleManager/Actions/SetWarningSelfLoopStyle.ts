/// <reference path='./index.ts'/>

class SetWarningSelfLoopStyle extends StyleManagerAction
{
    public Invoke(context : CanvasRenderingContext2D)
    {
        context.strokeStyle = this.Orange;
        context.lineWidth = 10;
        context.fillStyle = "transparent";
    }
}