/// <reference path='./index.ts'/>

class SetCorrectSelfLoopStyle extends StyleManagerAction
{
    public Invoke(context : CanvasRenderingContext2D) 
    {
        context.lineWidth = 10;
        context.strokeStyle = this.Green;
        context.fillStyle = "transparent";
    }
}