/// <reference path='./index.ts'/>

class SetIncorrectSelfLoopStyle extends StyleManagerAction
{
    public Invoke(context : CanvasRenderingContext2D)
    {
        context.strokeStyle = this.Red;
        context.lineWidth = 10;
        context.fillStyle = "transparent";
    }
}