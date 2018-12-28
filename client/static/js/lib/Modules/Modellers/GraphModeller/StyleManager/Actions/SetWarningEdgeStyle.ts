class SetWarningEdgeStyle extends StyleManagerAction
{
    public Invoke(context : CanvasRenderingContext2D)
    {
        context.strokeStyle = this.Orange;
        context.fillStyle ="black";
        context.lineWidth = 10;
    }
}