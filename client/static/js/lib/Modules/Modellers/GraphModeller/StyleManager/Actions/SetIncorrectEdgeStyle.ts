class SetIncorrectEdgeStyle extends StyleManagerAction
{
    public Invoke(context : CanvasRenderingContext2D)
    {
        context.fillStyle   = "white";
        context.strokeStyle = this.Red;
        context.lineWidth   = 10;
    }
}