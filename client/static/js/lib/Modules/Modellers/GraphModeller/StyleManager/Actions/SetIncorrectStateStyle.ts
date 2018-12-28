/// <reference path='./index.ts'/>

class SetIncorrectStateStyle extends StyleManagerAction
{
    public Invoke(context : CanvasRenderingContext2D) {
        context.fillStyle =  "white";
        context.strokeStyle = this.Red;
        context.lineWidth = 15;
    }
}