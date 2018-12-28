/// <reference path='./StyleManagerAction.ts'/>

class SetCorrectStateStyle extends StyleManagerAction
{
    public Invoke(context : CanvasRenderingContext2D)
    {
        context.fillStyle = "white";
        context.strokeStyle = this.Green;
        context.lineWidth = 15;
    }
}